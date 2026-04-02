<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmCrop;
use App\Models\FarmInventory;
use App\Models\FarmTransaction;
use App\Models\SeedType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FarmAdminController extends Controller
{
    // ═══════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════

    public function index()
    {
        $stats = [
            'total_seeds'       => SeedType::count(),
            'active_seeds'      => SeedType::where('is_active', true)->count(),
            'growing_crops'     => FarmCrop::where('status', 'growing')->count(),
            'ripe_crops'        => FarmCrop::where('status', 'ripe')->count(),
            'dead_crops'        => FarmCrop::where('status', 'dead')->count(),
            'total_sold_pt'     => FarmTransaction::where('type', 'sell_fruit')->sum('total_pt'),
            'total_bought_pt'   => FarmTransaction::where('type', 'buy_seed')->sum('total_pt'),
            'today_sell_pt'     => FarmTransaction::where('type', 'sell_fruit')->whereDate('created_at', today())->sum('total_pt'),
            'today_buy_pt'      => FarmTransaction::where('type', 'buy_seed')->whereDate('created_at', today())->sum('total_pt'),
        ];

        $recentTx = FarmTransaction::with(['user', 'seedType'])
            ->latest()->take(10)->get();

        return view('admin.farm.index', compact('stats', 'recentTx'));
    }

    // ═══════════════════════════════════════════════
    // CRUD HẠT GIỐNG
    // ═══════════════════════════════════════════════

    public function seeds()
    {
        $seeds = SeedType::orderBy('sort_order')->get();
        return view('admin.farm.seeds', compact('seeds'));
    }

    public function storeSeed(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:80',
            'emoji'           => 'required|string|max:10',
            'description'     => 'nullable|string|max:255',
            'price_buy'       => 'required|numeric|min:1',
            'price_sell_base' => 'required|numeric|min:1',
            'grow_time_mins'  => 'required|integer|min:1',
            'max_waterings'   => 'required|integer|min:1|max:20',
            'lucky_chance'    => 'required|numeric|min:0', // Sẽ chia 100 nếu > 1
            'is_active'       => 'boolean',
            'sort_order'      => 'integer|min:0',
        ]);

        $lucky = (float) $validated['lucky_chance'];
        if ($lucky > 1) $lucky = $lucky / 100;
        if ($lucky > 1) $lucky = 1; // Giới hạn tối đa 100%

        $data = $validated;
        $data['lucky_chance'] = $lucky;
        $data['slug'] = Str::slug($data['name']) . '-' . now()->timestamp;
        $data['is_active'] = $request->boolean('is_active');

        SeedType::create($data);
        return back()->with('success', "Đã thêm hạt giống {$data['name']}!");
    }

    public function updateSeed(Request $request, SeedType $seed)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:80',
            'emoji'           => 'required|string|max:10',
            'description'     => 'nullable|string|max:255',
            'price_buy'       => 'required|numeric|min:1',
            'price_sell_base' => 'required|numeric|min:1',
            'grow_time_mins'  => 'required|integer|min:1',
            'max_waterings'   => 'required|integer|min:1|max:20',
            'lucky_chance'    => 'required|numeric|min:0',
            'is_active'       => 'boolean',
            'sort_order'      => 'integer|min:0',
        ]);

        $lucky = (float) $validated['lucky_chance'];
        if ($lucky > 1) $lucky = $lucky / 100;
        if ($lucky > 1) $lucky = 1;

        $data = $validated;
        $data['lucky_chance'] = $lucky;
        $data['is_active'] = $request->boolean('is_active');
        $seed->update($data);

        return response()->json(['success' => true, 'message' => "Đã cập nhật {$seed->name}!"]);
    }

    public function deleteSeed(SeedType $seed)
    {
        if (FarmCrop::where('seed_type_id', $seed->id)->where('status', 'growing')->exists()) {
            return response()->json(['success' => false, 'message' => 'Có cây đang trồng loại này! Không thể xóa.']);
        }
        $seed->delete();
        return response()->json(['success' => true, 'message' => "Đã xóa {$seed->name}!"]);
    }

    // ═══════════════════════════════════════════════
    // LỊCH SỬ GIAO DỊCH
    // ═══════════════════════════════════════════════

    public function transactions(Request $request)
    {
        $query = FarmTransaction::with(['user', 'seedType'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('seed_id')) {
            $query->where('seed_type_id', $request->seed_id);
        }

        $transactions = $query->paginate(30);
        $seeds        = SeedType::orderBy('name')->get();

        $totals = [
            'buy'     => FarmTransaction::where('type', 'buy_seed')->sum('total_pt'),
            'sell'    => FarmTransaction::where('type', 'sell_fruit')->sum('total_pt'),
            'harvest' => FarmTransaction::where('type', 'harvest')->sum('quantity'),
        ];

        return view('admin.farm.transactions', compact('transactions', 'seeds', 'totals'));
    }
}
