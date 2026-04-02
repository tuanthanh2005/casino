<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavService;
use App\Models\NavOrder;
use App\Models\NavSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NavAdminController extends Controller
{
    /* ==================== SERVICES ==================== */

    public function services()
    {
        $services = NavService::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.nav.services', compact('services'));
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:200',
            'description'          => 'nullable|string',
            'requirements'         => 'nullable|string',
            'price'                => 'required|numeric|min:0',
            'appeal_deadline_days' => 'required|integer|min:1|max:365',
            'icon'                 => 'nullable|string|max:100',
            'color'                => 'nullable|string|max:20',
            'is_active'            => 'boolean',
            'sort_order'           => 'nullable|integer',
        ]);
        $data['slug']     = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        // Ensure unique slug
        $base = $data['slug'];
        $i = 1;
        while (NavService::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $i++;
        }

        NavService::create($data);
        return back()->with('success', 'Thêm dịch vụ thành công!');
    }

    public function updateService(Request $request, $id)
    {
        $service = NavService::findOrFail($id);
        $data = $request->validate([
            'name'                 => 'required|string|max:200',
            'description'          => 'nullable|string',
            'requirements'         => 'nullable|string',
            'price'                => 'required|numeric|min:0',
            'appeal_deadline_days' => 'required|integer|min:1|max:365',
            'icon'                 => 'nullable|string|max:100',
            'color'                => 'nullable|string|max:20',
            'sort_order'           => 'nullable|integer',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $service->update($data);
        return back()->with('success', 'Cập nhật dịch vụ thành công!');
    }

    public function deleteService($id)
    {
        $service = NavService::findOrFail($id);
        if ($service->orders()->count() > 0) {
            return back()->with('error', 'Không thể xóa dịch vụ đã có đơn hàng!');
        }
        $service->delete();
        return back()->with('success', 'Đã xóa dịch vụ!');
    }

    /* ==================== ORDERS ==================== */

    public function orders(Request $request)
    {
        $query = NavOrder::with(['service', 'user'])->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->search) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('order_code', 'like', "%$q%")
                   ->orWhere('tiktok_username', 'like', "%$q%")
                   ->orWhere('customer_name', 'like', "%$q%");
            });
        }

        $orders    = $query->paginate(20)->withQueryString();
        $services  = NavService::orderBy('name')->get();
        $pendingCount = NavOrder::where('status', 'paid')->count();

        return view('admin.nav.orders', compact('orders', 'services', 'pendingCount'));
    }

    public function orderDetail($id)
    {
        $order = NavOrder::with(['service', 'user'])->findOrFail($id);
        return view('admin.nav.order-detail', compact('order'));
    }

    public function approvePayment(Request $request, $id)
    {
        $order = NavOrder::findOrFail($id);
        $order->update([
            'status'               => 'processing',
            'payment_verified_at'  => now(),
            'admin_notes'          => $request->admin_notes ?? $order->admin_notes,
        ]);
        return back()->with('success', 'Đã xác nhận thanh toán, đơn chuyển sang "Đang xử lý".');
    }

    public function completeOrder(Request $request, $id)
    {
        $order = NavOrder::findOrFail($id);
        $order->update([
            'status'         => 'completed',
            'appeal_sent_at' => now(),
            'admin_notes'    => $request->admin_notes ?? $order->admin_notes,
        ]);
        return back()->with('success', 'Đơn hàng đã hoàn thành!');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = NavOrder::findOrFail($id);
        $request->validate(['status' => 'required|in:pending_payment,paid,processing,completed,cancelled']);
        $order->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes ?? $order->admin_notes,
        ]);
        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /** Tạo bài kháng cáo - trả JSON để frontend hiển thị */
    public function generateAppeal($id)
    {
        $order = NavOrder::with('service')->findOrFail($id);
        $letter = $order->generateAppealLetter();
        return response()->json(['letter' => $letter]);
    }

    /* ==================== SETTINGS ==================== */

    public function settings()
    {
        $settings = NavSetting::all_settings();
        return view('admin.nav.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $fields = ['bank_name', 'bank_account', 'bank_owner', 'bank_bin', 'pt_enabled', 'bank_enabled'];
        foreach ($fields as $field) {
            NavSetting::set($field, $request->input($field, ''));
        }
        return back()->with('success', 'Đã lưu cài đặt thanh toán!');
    }
}
