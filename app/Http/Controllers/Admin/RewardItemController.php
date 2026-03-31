<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RewardItemController extends Controller
{
    public function index()
    {
        $items = RewardItem::latest()->paginate(15);
        return view('admin.rewards.index', compact('items'));
    }

    public function create()
    {
        return view('admin.rewards.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'point_price' => 'required|numeric|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage($request->file('image'));
        }

        RewardItem::create($validated);

        return redirect()->route('admin.rewards.index')->with('success', 'Thêm phần thưởng thành công!');
    }

    public function edit(RewardItem $reward)
    {
        return view('admin.rewards.edit', compact('reward'));
    }

    public function update(Request $request, RewardItem $reward)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'point_price' => 'required|numeric|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($reward->image && file_exists(public_path($reward->image))) {
                unlink(public_path($reward->image));
            }
            $validated['image'] = $this->uploadImage($request->file('image'));
        }

        $reward->update($validated);

        return redirect()->route('admin.rewards.index')->with('success', 'Cập nhật phần thưởng thành công!');
    }

    public function destroy(RewardItem $reward)
    {
        if ($reward->image && file_exists(public_path($reward->image))) {
            unlink(public_path($reward->image));
        }
        $reward->delete();

        return redirect()->route('admin.rewards.index')->with('success', 'Đã xóa phần thưởng.');
    }

    /**
     * Upload image to public/uploads/rewards (không dùng storage)
     */
    private function uploadImage($file): string
    {
        $dir = public_path('uploads/rewards');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'uploads/rewards/' . $filename;
    }
}
