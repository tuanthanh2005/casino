<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::query()->latest()->paginate(15);

        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $validated['slug'] = $this->ensureUniqueSlug($validated['slug'] ?? Str::slug($validated['title']));
        $validated['created_by'] = auth()->id();
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $this->uploadImage($request->file('cover_image'));
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Tạo bài blog thành công.');
    }

    public function edit(BlogPost $blog_post)
    {
        return view('admin.blog.edit', ['post' => $blog_post]);
    }

    public function update(Request $request, BlogPost $blog_post)
    {
        $validated = $this->validatePayload($request, $blog_post->id);
        $targetSlug = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['slug'] = $this->ensureUniqueSlug($targetSlug, $blog_post->id);
        $validated['is_published'] = $request->boolean('is_published');

        if ($validated['is_published']) {
            $validated['published_at'] = $blog_post->published_at ?? now();
        } else {
            $validated['published_at'] = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($blog_post->cover_image) {
                Storage::disk('public_uploads')->delete($blog_post->cover_image);
            }
            $validated['cover_image'] = $this->uploadImage($request->file('cover_image'));
        }

        if ($request->boolean('remove_cover_image')) {
            if ($blog_post->cover_image) {
                Storage::disk('public_uploads')->delete($blog_post->cover_image);
            }
            $validated['cover_image'] = null;
        }

        $blog_post->update($validated);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Cập nhật bài blog thành công.');
    }

    public function destroy(BlogPost $blog_post)
    {
        if ($blog_post->cover_image) {
            Storage::disk('public_uploads')->delete($blog_post->cover_image);
        }

        $blog_post->delete();

        return redirect()->route('admin.blog-posts.index')->with('success', 'Đã xóa bài blog.');
    }

    private function validatePayload(Request $request, ?int $postId = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($postId),
            ],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string|min:30',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'is_published' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'remove_cover_image' => 'nullable|boolean',
        ]);
    }

    private function ensureUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug);

        if ($base === '') {
            $base = 'blog-post';
        }

        $candidate = $base;
        $counter = 2;

        while (
            BlogPost::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function uploadImage($file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public_uploads')->putFileAs('uploads/blog', $file, $filename);

        return 'uploads/blog/' . $filename;
    }
}
