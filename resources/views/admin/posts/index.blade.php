@extends('layouts.admin')

@section('page_title', 'All Posts')

@section('header_actions')
<a href="/admin/posts/create" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.8125rem;">+ Create New Post</a>
@endsection

@section('admin_content')
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <form action="/admin/posts" method="GET" style="display: flex; gap: 1rem; flex-grow: 1; max-width: 500px;">
            <input type="text" name="q" placeholder="Search posts..." value="{{ request('q') }}" style="flex-grow: 1; padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8125rem;">
            <select name="category" style="padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8125rem;">
                <option value="">All Categories</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">Filter</button>
        </form>
        <div>
            <button type="button" id="btnBulkDelete" class="btn" style="padding: 0.5rem 1.25rem; font-size: 0.8125rem; background: #ef4444; color: white; border: none; border-radius: 6px;">Delete Selected</button>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="table" style="margin-top: 0; width: 100%;">
            <thead>
                <tr>
                    <th style="padding-left: 2rem; width: 40px;">
                        <input type="checkbox" id="selectAll" style="cursor: pointer;">
                    </th>
                    <th>Lang</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th style="padding-right: 2rem; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                <tr>
                    <td style="padding-left: 2rem;">
                        <input type="checkbox" value="{{ $post->id }}" class="post-checkbox" style="cursor: pointer;">
                    </td>
                    <td>
                        <span class="badge {{ $post->lang == 'en' ? 'bg-primary' : 'bg-danger' }}" style="font-size: 0.65rem;">
                            {{ strtoupper($post->lang) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight: 700; color: #0284c7; display: block; margin-bottom: 0.25rem;">{{ $post->title }}</span>
                        <span style="font-size: 0.75rem; color: #94a3b8;">/{{ $post->slug }}</span>
                    </td>
                    <td><span style="font-size: 0.8125rem; background: #f1f5f9; padding: 0.25rem 0.625rem; border-radius: 4px; font-weight: 500;">{{ $post->category->name }}</span></td>
                    <td>
                        <span class="badge {{ $post->status === 'published' ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.65rem; text-transform: uppercase;">
                            {{ $post->status }}
                        </span>
                    </td>
                    <td><span style="font-size: 0.8125rem;">{{ $post->author->name }}</span></td>
                    <td><span style="font-size: 0.8125rem; color: #64748b;">{{ $post->published_at?->format('M d, Y') ?? 'Draft' }}</span></td>
                    <td style="padding-right: 2rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <a href="/admin/posts/{{ $post->id }}/edit" class="btn" style="padding: 0.4rem 0.6rem; font-size: 0.75rem; background: #f1f5f9; color: #475569;">Edit</a>
                            <button type="button" onclick="if(confirm('Are you sure?')) { document.getElementById('singleDeleteForm').action = '/admin/posts/{{ $post->id }}'; document.getElementById('singleDeleteForm').submit(); }" class="btn" style="padding: 0.4rem 0.6rem; font-size: 0.75rem; background: #fee2e2; color: #991b1b; border: none;">Delete</button>
                            <a href="/blog/{{ $post->slug }}" target="_blank" class="btn" style="padding: 0.4rem 0.6rem; font-size: 0.75rem; background: #e0f2fe; color: #0369a1;">Live</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="padding: 2rem; border-top: 1px solid #f1f5f9; background: #f8fafc;">
        {{ $posts->links() }}
    </div>
</div>

<form id="singleDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="bulkDeleteForm" action="{{ route('admin.posts.bulkDelete') }}" method="POST" style="display: none;">
    @csrf
    <div id="bulkDeleteInputs"></div>
</form>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.post-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
        });
    });

    document.getElementById('btnBulkDelete').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.post-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one post to delete.');
            return;
        }

        if (confirm('Are you sure you want to delete the selected posts?')) {
            const form = document.getElementById('bulkDeleteForm');
            const inputsDiv = document.getElementById('bulkDeleteInputs');
            inputsDiv.innerHTML = ''; // Clear previous

            checkboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                inputsDiv.appendChild(input);
            });

            form.submit();
        }
    });
</script>
@endsection
