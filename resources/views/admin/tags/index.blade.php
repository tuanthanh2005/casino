@extends('layouts.admin')

@section('page_title', __('Tag Management'))

@section('header_actions')
<a href="{{ route('admin.tags.create') }}" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 99px; background: #0f172a; border: none;">
    {{ __('Create New Tag') }}
</a>
@endsection

@section('admin_content')
<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-secondary small text-uppercase">{{ __('Name') }}</th>
                    <th class="py-3 text-secondary small text-uppercase">{{ __('Slug') }}</th>
                    <th class="pe-4 py-3 text-secondary small text-uppercase text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tags as $tag)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark"># {{ $tag->name }}</div>
                    </td>
                    <td>
                        <code class="small text-secondary">{{ $tag->slug }}</code>
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-outline-dark border-0 bg-light rounded-3">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('{{ __('Delete this tag?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 bg-light rounded-3">
                                    {{ __('Delete') }}
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
