<?php

namespace App\Http\Controllers;

use App\Models\DepositOrder;
use App\Models\WithdrawalOrder;
use App\Services\TelegramNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    const TAX_RATE        = 0.02;
    const MB_BANK_ACCOUNT = '0783704196';
    const MB_BANK_NAME    = 'TRAN THANH TUAN';
    const MB_BANK_CODE    = 'MB';

    // ── Giới hạn: 5 lần/30 phút (tổng cả nạp lẫn rút) ──
    const RATE_LIMIT_MAX  = 5;
    const RATE_LIMIT_MINS = 30;

    // ═══════════════════════════════════════════════
    // RATE LIMIT HELPER
    // ═══════════════════════════════════════════════

    /**
     * Đếm số lần giao dịch (nạp + rút) của user trong RATE_LIMIT_MINS phút qua.
     * Trả về ['allowed' => bool, 'remaining' => int, 'retry_in' => int (phút)]
     */
    private function checkRateLimit(int $userId): array
    {
        $since = now()->subMinutes(self::RATE_LIMIT_MINS);

        $deposits = DepositOrder::where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->count();

        $withdrawals = WithdrawalOrder::where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->count();

        $total = $deposits + $withdrawals;

        if ($total < self::RATE_LIMIT_MAX) {
            return ['allowed' => true, 'count' => $total, 'remaining' => self::RATE_LIMIT_MAX - $total];
        }

        // Tìm giao dịch cũ nhất trong cửa sổ 30p để tính thời gian còn lại
        $oldestDeposit    = DepositOrder::where('user_id', $userId)->where('created_at', '>=', $since)->oldest()->value('created_at');
        $oldestWithdrawal = WithdrawalOrder::where('user_id', $userId)->where('created_at', '>=', $since)->oldest()->value('created_at');

        $candidates = array_filter([$oldestDeposit, $oldestWithdrawal]);
        $oldest     = $candidates ? min($candidates) : $since;

        // Thời gian còn phải đợi = 30p kể từ giao dịch cũ nhất
        $unlockAt  = \Carbon\Carbon::parse($oldest)->addMinutes(self::RATE_LIMIT_MINS);
        $retryMins = (int) ceil(now()->diffInMinutes($unlockAt, false));
        $retrySecs = (int) ceil(now()->diffInSeconds($unlockAt, false));

        return [
            'allowed'    => false,
            'count'      => $total,
            'remaining'  => 0,
            'retry_in'   => max(1, $retryMins),
            'retry_secs' => max(1, $retrySecs),
        ];
    }

    // ═══════════════════════════════════════════════
    // NẠP TIỀN
    // ═══════════════════════════════════════════════

    public function depositIndex()
    {
        $orders = DepositOrder::where('user_id', auth()->id())
            ->latest()->take(20)->get();

        $rate = $this->checkRateLimit(auth()->id());
        return view('payment.deposit', compact('orders', 'rate'));
    }

    public function depositStore(Request $request)
    {
        $user = auth()->user();

        // Rate limit check
        $rate = $this->checkRateLimit($user->id);
        if (!$rate['allowed']) {
            return response()->json([
                'success'    => false,
                'rate_limit' => true,
                'retry_in'   => $rate['retry_in'],
                'message'    => "⏳ Bạn đã giao dịch quá " . self::RATE_LIMIT_MAX . " lần trong " . self::RATE_LIMIT_MINS . " phút. Vui lòng chờ {$rate['retry_in']} phút nữa.",
            ]);
        }

        $request->validate([
            'method'      => 'required|in:bank_qr,card',
            'amount'      => 'required_if:method,bank_qr|nullable|numeric|min:10000',
            'card_type'   => 'required_if:method,card',
            'card_serial' => 'required_if:method,card|nullable|string',
            'card_pin'    => 'required_if:method,card|nullable|string',
            'card_amount' => 'required_if:method,card|nullable|in:10000,20000,50000,100000,200000,500000',
        ]);

        $orderCode = DepositOrder::generateCode();

        if ($request->method === 'bank_qr') {
            DepositOrder::create([
                'user_id'    => $user->id,
                'order_code' => $orderCode,
                'method'     => 'bank_qr',
                'amount'     => (float) $request->amount,
            ]);

            TelegramNotifier::send(
                "<b>📥 Đơn nạp mới (QR)</b>\n"
                . "Mã: <code>{$orderCode}</code>\n"
                . "User: {$user->name} (#{$user->id})\n"
                . "Số tiền: " . number_format((float) $request->amount, 0) . " VNĐ\n"
                . "Trạng thái: <b>pending</b>"
            );

            return response()->json([
                'success'    => true,
                'order_code' => $orderCode,
                'amount'     => $request->amount,
                'qr_url'     => $this->buildQrUrl($orderCode, (float) $request->amount),
                'remaining'  => $rate['remaining'] - 1,
                'message'    => "Đơn #{$orderCode} tạo thành công! Vui lòng chuyển khoản và chờ admin xác nhận.",
            ]);
        }

        // Thẻ cào
        $cardAmount = (float) $request->card_amount;
        DepositOrder::create([
            'user_id'     => $user->id,
            'order_code'  => $orderCode,
            'method'      => 'card',
            'amount'      => $cardAmount,
            'card_type'   => $request->card_type,
            'card_serial' => $request->card_serial,
            'card_pin'    => $request->card_pin,
            'card_amount' => $request->card_amount,
        ]);

        TelegramNotifier::send(
            "<b>📥 Đơn nạp mới (Thẻ cào)</b>\n"
            . "Mã: <code>{$orderCode}</code>\n"
            . "User: {$user->name} (#{$user->id})\n"
            . "Nhà mạng: {$request->card_type}\n"
            . "Mệnh giá: " . number_format($cardAmount, 0) . " VNĐ\n"
            . "Trạng thái: <b>pending</b>"
        );

        return response()->json([
            'success'    => true,
            'order_code' => $orderCode,
            'remaining'  => $rate['remaining'] - 1,
            'message'    => "Thẻ #{$orderCode} đã gửi thành công! Admin sẽ kiểm tra và duyệt sớm.",
        ]);
    }

    private function buildQrUrl(string $content, float $amount): string
    {
        $name = urlencode(self::MB_BANK_NAME);
        return "https://img.vietqr.io/image/" . self::MB_BANK_CODE . "-" . self::MB_BANK_ACCOUNT
            . "-compact2.jpg?amount={$amount}&addInfo={$content}&accountName={$name}";
    }

    // ═══════════════════════════════════════════════
    // RÚT TIỀN / ĐỔI THẺ
    // ═══════════════════════════════════════════════

    public function withdrawIndex()
    {
        $orders  = WithdrawalOrder::where('user_id', auth()->id())
            ->latest()->take(20)->get();
        $balance = (float) auth()->user()->balance_point;
        $rate    = $this->checkRateLimit(auth()->id());
        return view('payment.withdraw', compact('orders', 'balance', 'rate'));
    }

    public function withdrawStore(Request $request)
    {
        $user = auth()->user();

        // Rate limit check
        $rate = $this->checkRateLimit($user->id);
        if (!$rate['allowed']) {
            return response()->json([
                'success'    => false,
                'rate_limit' => true,
                'retry_in'   => $rate['retry_in'],
                'message'    => "⏳ Bạn đã giao dịch quá " . self::RATE_LIMIT_MAX . " lần trong " . self::RATE_LIMIT_MINS . " phút. Vui lòng chờ {$rate['retry_in']} phút nữa.",
            ]);
        }

        $request->validate([
            'method'       => 'required|in:bank_transfer,card',
            'points'       => 'required|numeric|min:10000',
            'bank_name'    => 'required_if:method,bank_transfer|nullable|string|max:100',
            'bank_account' => 'required_if:method,bank_transfer|nullable|string|max:30',
            'bank_holder'  => 'required_if:method,bank_transfer|nullable|string|max:100',
            'card_type'    => 'required_if:method,card|nullable|string',
        ]);

        $points    = (float) $request->points;
        $taxAmount = round($points * self::TAX_RATE, 2);
        $netAmount = round($points - $taxAmount, 2);

        if ($user->balance_point < $points) {
            return response()->json(['success' => false, 'message' => 'Số dư không đủ để rút.']);
        }

        // Chặn khi có đơn đang chờ
        $hasPending = WithdrawalOrder::where('user_id', $user->id)
            ->where('status', 'pending')->exists();
        if ($hasPending) {
            return response()->json(['success' => false, 'message' => 'Bạn đang có đơn rút đang chờ xử lý. Vui lòng chờ admin duyệt.']);
        }

        $orderCode = WithdrawalOrder::generateCode();

        DB::transaction(function () use ($user, $points, $taxAmount, $netAmount, $request, $orderCode) {
            $user->decrement('balance_point', $points);
            WithdrawalOrder::create([
                'user_id'      => $user->id,
                'order_code'   => $orderCode,
                'method'       => $request->method,
                'points_used'  => $points,
                'tax_rate'     => self::TAX_RATE,
                'tax_amount'   => $taxAmount,
                'net_amount'   => $netAmount,
                'bank_name'    => $request->bank_name,
                'bank_account' => $request->bank_account,
                'bank_holder'  => $request->bank_holder,
                'card_type'    => $request->card_type,
            ]);
        });

        $withdrawMethod = $request->method === 'bank_transfer' ? 'Ngân hàng' : 'Đổi thẻ';
        TelegramNotifier::send(
            "<b>📤 Đơn rút mới</b>\n"
            . "Mã: <code>{$orderCode}</code>\n"
            . "User: {$user->name} (#{$user->id})\n"
            . "Phương thức: {$withdrawMethod}\n"
            . "Điểm rút: " . number_format($points, 0) . " PT\n"
            . "Thực nhận: " . number_format($netAmount, 0) . "\n"
            . "Trạng thái: <b>pending</b>"
        );

        return response()->json([
            'success'    => true,
            'order_code' => $orderCode,
            'net_amount' => number_format($netAmount, 0),
            'remaining'  => $rate['remaining'] - 1,
            'message'    => "Đơn rút #{$orderCode} đã gửi! Bạn sẽ nhận " . number_format($netAmount, 0) . " (sau thuế 2%). Admin sẽ xử lý sớm.",
        ]);
    }
}
