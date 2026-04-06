@extends('layouts.admin')

@section('page_title', 'Category Management')

@section('header_actions')
<a href="{{ route('admin.categories.create') }}" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 99px; background: #0f172a; border: none;">
    Create New Category
</a>
@endsection

@section('admin_content')
<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-secondary small text-uppercase">Language</th>
                    <th class="py-3 text-secondary small text-uppercase">Name</th>
                    <th class="py-3 text-secondary small text-uppercase">Slug / URL</th>
                    <th class="py-3 text-secondary small text-uppercase text-center">Post Count</th>
                    <th class="pe-4 py-3 text-secondary small text-uppercase text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td class="ps-4">
                        <span class="badge {{ $category->lang == 'en' ? 'bg-primary' : 'bg-danger' }}" style="font-size: 0.65rem;">
                            {{ strtoupper($category->lang) }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $category->name }}</div>
                    </td>
                    <td>
                        <code class="small text-secondary">/category/{{ $category->slug }}</code>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark fw-normal border px-3 py-2 rounded-pill">
                            {{ $category->posts_count }}
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-dark border-0 bg-light rounded-3">
                                Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure? All posts in this category may become uncategorized.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 bg-light rounded-3">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
