<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category', 'author')->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::where('lang', app()->getLocale())->get();
        if ($categories->isEmpty()) {
            $categories = Category::all(); // Fallback if no specific categories for this lang
        }
        $tags = Tag::all();
        return view('admin.posts.form', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug',
            'content' => 'required',
            'excerpt' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'is_featured' => 'boolean',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'lang' => 'required|in:en,vi',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $fileName = time() . '_' . $image->getClientOriginalName();
            Storage::disk('public_uploads')->putFileAs('uploads/posts', $image, $fileName);
            $validated['featured_image'] = $fileName;
        }

        $validated['author_id'] = auth()->id() ?? 1; // Default to ID 1 if not logged in
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        $post = Post::create($validated);

        $post->tags()->sync($this->resolveTagIds($request->tags_input ?? ''));

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        $categories = Category::where('lang', $post->lang)->get();
        $tags = Tag::all();
        return view('admin.posts.form', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => "required|unique:posts,slug,{$post->id}",
            'content' => 'required',
            'excerpt' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'is_featured' => 'boolean',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'lang' => 'required|in:en,vi',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image && Storage::disk('public_uploads')->exists('uploads/posts/' . $post->featured_image)) {
                Storage::disk('public_uploads')->delete('uploads/posts/' . $post->featured_image);
            }
            $image = $request->file('featured_image');
            $fileName = time() . '_' . $image->getClientOriginalName();
            Storage::disk('public_uploads')->putFileAs('uploads/posts', $image, $fileName);
            $validated['featured_image'] = $fileName;
        }

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        $post->tags()->sync($this->resolveTagIds($request->tags_input ?? ''));

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image && Storage::disk('public_uploads')->exists('uploads/posts/' . $post->featured_image)) {
            Storage::disk('public_uploads')->delete('uploads/posts/' . $post->featured_image);
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:posts,id'
        ]);

        $posts = Post::whereIn('id', $request->ids)->get();

        foreach ($posts as $post) {
            if ($post->featured_image && Storage::disk('public_uploads')->exists('uploads/posts/' . $post->featured_image)) {
                Storage::disk('public_uploads')->delete('uploads/posts/' . $post->featured_image);
            }
            $post->delete();
        }

        return redirect()->route('admin.posts.index')->with('success', 'Selected posts deleted successfully.');
    }

    /**
     * Resolve a comma-separated string of tag IDs or tag names into an array of valid integer IDs.
     * Entries that are numeric are used directly (after verifying they exist).
     * Entries that are not numeric are looked up by tag name.
     * Unresolvable entries are silently ignored.
     */
    private function resolveTagIds(string $input): array
    {
        if (trim($input) === '') {
            return [];
        }

        $parts = array_filter(array_map('trim', explode(',', $input)));

        $numericIds = [];
        $names      = [];

        foreach ($parts as $part) {
            if (ctype_digit($part)) {
                $numericIds[] = (int) $part;
            } else {
                $names[] = $part;
            }
        }

        $resolvedIds = [];

        // Validate numeric IDs actually exist
        if (!empty($numericIds)) {
            $resolvedIds = Tag::whereIn('id', $numericIds)->pluck('id')->toArray();
        }

        // Look up by name for text entries
        if (!empty($names)) {
            $byName = Tag::whereIn('name', $names)->pluck('id')->toArray();
            $resolvedIds = array_merge($resolvedIds, $byName);
        }

        return array_unique($resolvedIds);
    }
}
