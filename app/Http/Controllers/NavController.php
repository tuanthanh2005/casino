<?php

namespace App\Http\Controllers;

use App\Models\NavService;
use App\Models\NavOrder;
use App\Models\NavSetting;
use App\Services\TelegramNotifier;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NavController extends Controller
{
    /** Trang danh sách dịch vụ */
    public function index()
    {
        $services = NavService::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        return view('nav.index', compact('services'));
    }

    /** Trang chi tiết dịch vụ + form đăng ký */
    public function show(string $slug)
    {
        $service = NavService::findBySlug($slug);
        if (!$service) abort(404);
        return view('nav.show', compact('service'));
    }

    /** Lưu đơn hàng */
    public function store(Request $request, string $slug)
    {
        $service = NavService::findBySlug($slug);
        if (!$service) abort(404);

        $request->validate([
            'tiktok_username'  => 'required|string|max:100',
            'registered_email' => 'nullable|email|max:200',
            'registered_phone' => 'nullable|string|max:20',
            'violation_type'   => 'nullable|string|max:200',
            'violation_date'   => 'nullable|date',
            'follower_count'   => 'nullable|integer|min:0',
            'account_notes'    => 'nullable|string|max:1000',
            'customer_name'    => 'required|string|max:200',
            'customer_contact' => 'required|string|max:200',
            'payment_method'   => 'required|in:points,bank',
            'id_card_front'    => 'nullable|image|max:5120',
            'id_card_back'     => 'nullable|image|max:5120',
            'screenshot_path'  => 'nullable|image|max:5120',
        ]);

        // Upload ảnh vào disk public_uploads theo cấu hình filesystem.
        $uploadFile = function (string $field) use ($request): ?string {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . now()->timestamp . '_' . Str::lower(Str::random(10)) . '.' . $file->getClientOriginalExtension();
                Storage::disk('public_uploads')->putFileAs('uploads/nav', $file, $filename);
                return 'uploads/nav/' . $filename;
            }
            return null;
        };

        $orderCode = NavOrder::generateCode();

        // Nếu thanh toán bằng PT
        if ($request->payment_method === 'points') {
            $user = auth()->user();
            if ($user->balance_point < $service->price) {
                return back()->withErrors(['payment_method' => 'Số dư PT không đủ. Cần ' . number_format((float)$service->price) . ' PT.'])->withInput();
            }
            // Trừ PT
            $user->decrement('balance_point', (float)$service->price);
        }

        $violationDate = $request->violation_date ? Carbon::parse($request->violation_date) : null;
        $appealDeadline = $violationDate
            ? $violationDate->copy()->addDays($service->appeal_deadline_days)
            : now()->addDays($service->appeal_deadline_days);

        $order = NavOrder::create([
            'user_id'          => auth()->id(),
            'service_id'       => $service->id,
            'order_code'       => $orderCode,
            'status'           => $request->payment_method === 'points' ? 'paid' : 'pending_payment',
            'payment_method'   => $request->payment_method,
            'tiktok_username'  => $request->tiktok_username,
            'registered_email' => $request->registered_email,
            'registered_phone' => $request->registered_phone,
            'violation_type'   => $request->violation_type,
            'violation_date'   => $violationDate,
            'follower_count'   => $request->follower_count,
            'account_notes'    => $request->account_notes,
            'customer_name'    => $request->customer_name,
            'customer_contact' => $request->customer_contact,
            'amount'           => $service->price,
            'transfer_content' => $orderCode,
            'id_card_front'    => $uploadFile('id_card_front'),
            'id_card_back'     => $uploadFile('id_card_back'),
            'screenshot_path'  => $uploadFile('screenshot_path'),
            'appeal_deadline'  => $appealDeadline,
            'payment_confirmed_at' => $request->payment_method === 'points' ? now() : null,
        ]);

        TelegramNotifier::send(
            "<b>🛡️ Đơn NAV mới</b>\n"
            . "Mã: <code>{$order->order_code}</code>\n"
            . "Dịch vụ: {$service->name}\n"
            . "User: " . auth()->user()->name . " (#" . auth()->id() . ")\n"
            . "Thanh toán: " . ($request->payment_method === 'points' ? 'PT' : 'Chuyển khoản') . "\n"
            . "Số tiền: " . number_format((float) $service->price, 0) . "\n"
            . "Trạng thái: <b>{$order->status}</b>"
        );

        if ($request->payment_method === 'points') {
            return redirect()->route('nav.success', $order->order_code)
                ->with('success', 'Đơn hàng đã được tạo và thanh toán bằng PT thành công!');
        }

        return redirect()->route('nav.payment', $order->order_code);
    }

    /** Trang thanh toán QR */
    public function payment(string $code)
    {
        $order = NavOrder::with('service')
            ->where('order_code', $code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status === 'paid' || $order->status === 'processing' || $order->status === 'completed') {
            return redirect()->route('nav.success', $code);
        }

        $qrUrl    = NavSetting::vietQrUrl($code, $order->amount);
        $settings = NavSetting::all_settings();

        return view('nav.payment', compact('order', 'qrUrl', 'settings'));
    }

    /** Người dùng xác nhận đã chuyển khoản */
    public function confirmPayment(string $code)
    {
        $order = NavOrder::where('order_code', $code)
            ->where('user_id', auth()->id())
            ->where('status', 'pending_payment')
            ->firstOrFail();

        $order->update([
            'status'               => 'paid',
            'payment_confirmed_at' => now(),
        ]);

        TelegramNotifier::send(
            "<b>💸 NAV xác nhận đã chuyển khoản</b>\n"
            . "Mã: <code>{$order->order_code}</code>\n"
            . "User ID: {$order->user_id}\n"
            . "Trạng thái mới: <b>paid</b>"
        );

        return redirect()->route('nav.success', $code)
            ->with('success', 'Đã xác nhận thanh toán! Admin sẽ xử lý đơn của bạn sớm nhất.');
    }

    /** Trang thành công */
    public function success(string $code)
    {
        $order = NavOrder::with('service')
            ->where('order_code', $code)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        return view('nav.success', compact('order'));
    }

    /** Lịch sử đơn của user */
    public function myOrders()
    {
        $orders = NavOrder::with('service')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('nav.my-orders', compact('orders'));
    }
}
