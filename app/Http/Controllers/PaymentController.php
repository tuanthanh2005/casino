<?php

namespace App\Http\Controllers;

use App\Models\DepositOrder;
use App\Models\WithdrawalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    const TAX_RATE = 0.02; // 2%
    const MB_BANK_ACCOUNT = '0783704196';
    const MB_BANK_NAME    = 'TRAN THANH TUAN';
    const MB_BANK_CODE    = 'MB';

    // ═══════════════════════════
    // NẠP TIỀN
    // ═══════════════════════════

    public function depositIndex()
    {
        $orders = DepositOrder::where('user_id', auth()->id())
            ->latest()->take(20)->get();
        return view('payment.deposit', compact('orders'));
    }

    public function depositStore(Request $request)
    {
        $request->validate([
            'method'      => 'required|in:bank_qr,card',
            'amount'      => 'required_if:method,bank_qr|nullable|numeric|min:10000',
            'card_type'   => 'required_if:method,card',
            'card_serial' => 'required_if:method,card|nullable|string',
            'card_pin'    => 'required_if:method,card|nullable|string',
            'card_amount' => 'required_if:method,card|nullable|in:10000,20000,50000,100000,200000,500000',
        ]);

        $user      = auth()->user();
        $orderCode = DepositOrder::generateCode();

        if ($request->method === 'bank_qr') {
            $order = DepositOrder::create([
                'user_id'    => $user->id,
                'order_code' => $orderCode,
                'method'     => 'bank_qr',
                'amount'     => (float) $request->amount,
            ]);
            return response()->json([
                'success'     => true,
                'order_code'  => $orderCode,
                'amount'      => $request->amount,
                'qr_url'      => $this->buildQrUrl($orderCode, (float) $request->amount),
                'message'     => "Đơn #{$orderCode} tạo thành công! Vui lòng chuyển khoản và chờ admin xác nhận.",
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

        return response()->json([
            'success'    => true,
            'order_code' => $orderCode,
            'message'    => "Thẻ #{$orderCode} đã gửi thành công! Admin sẽ kiểm tra và duyệt sớm.",
        ]);
    }

    private function buildQrUrl(string $content, float $amount): string
    {
        $name = urlencode(self::MB_BANK_NAME);
        return "https://img.vietqr.io/image/" . self::MB_BANK_CODE . "-" . self::MB_BANK_ACCOUNT
            . "-compact2.jpg?amount={$amount}&addInfo={$content}&accountName={$name}";
    }

    // ═══════════════════════════
    // RÚT TIỀN / ĐỔI THẺ
    // ═══════════════════════════

    public function withdrawIndex()
    {
        $orders = WithdrawalOrder::where('user_id', auth()->id())
            ->latest()->take(20)->get();
        $balance = (float) auth()->user()->balance_point;
        return view('payment.withdraw', compact('orders', 'balance'));
    }

    public function withdrawStore(Request $request)
    {
        $request->validate([
            'method'       => 'required|in:bank_transfer,card',
            'points'       => 'required|numeric|min:10000',
            // Bank
            'bank_name'    => 'required_if:method,bank_transfer|nullable|string|max:100',
            'bank_account' => 'required_if:method,bank_transfer|nullable|string|max:30',
            'bank_holder'  => 'required_if:method,bank_transfer|nullable|string|max:100',
            // Card
            'card_type'    => 'required_if:method,card|nullable|string',
        ]);

        $user       = auth()->user();
        $points     = (float) $request->points;
        $taxAmount  = round($points * self::TAX_RATE, 2);
        $netAmount  = round($points - $taxAmount, 2);

        if ($user->balance_point < $points) {
            return response()->json(['success' => false, 'message' => 'Số dư không đủ để rút.']);
        }

        // Chặn rút khi có đơn đang chờ
        $hasPending = WithdrawalOrder::where('user_id', $user->id)
            ->where('status', 'pending')->exists();
        if ($hasPending) {
            return response()->json(['success' => false, 'message' => 'Bạn đang có đơn rút đang chờ xử lý. Vui lòng chờ.']);
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

        return response()->json([
            'success'    => true,
            'order_code' => $orderCode,
            'net_amount' => number_format($netAmount, 0),
            'message'    => "Đơn rút #{$orderCode} đã gửi! Bạn sẽ nhận {$netAmount} (sau thuế 2%). Admin sẽ xử lý sớm.",
        ]);
    }
}
