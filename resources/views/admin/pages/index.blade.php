@extends('layouts.admin')

@section('page_title', __('Static Pages Management'))

@section('header_actions')
<a href="{{ route('admin.pages.create') }}" class="btn btn-primary fw-bold" style="border-radius: 8px;">
    {{ __('+ Add New Page') }}
</a>
@endsection

@section('admin_content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <ul class="nav nav-tabs px-4 pt-3 border-bottom-0" style="gap: 0.5rem;">
            <li class="nav-item">
                <a class="nav-link {{ !request('lang') || request('lang') == 'vi' ? 'active fw-bold' : 'text-secondary' }}" 
                   href="?lang=vi" style="border-radius: 8px 8px 0 0;">Tiếng Việt</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('lang') == 'en' ? 'active fw-bold' : 'text-secondary' }}" 
                   href="?lang=en" style="border-radius: 8px 8px 0 0;">English</a>
            </li>
        </ul>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="px-4 py-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ __('Title') }}</th>
                        <th class="px-4 py-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ __('URL / Slug') }}</th>
                        <th class="px-4 py-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-end text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="fw-bold text-dark">{{ $page->title }}</span>
                        </td>
                        <td class="px-4 py-3 text-secondary small">
                            /page/{{ $page->slug }}
                        </td>
                        <td class="px-4 py-3">
                            @if($page->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ __('Visibility: Public') }}</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ __('Hidden') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            <a href="/page/{{ $page->slug }}" target="_blank" class="btn btn-sm btn-light border me-1">{{ __('View') }}</a>
                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-light border me-1">{{ __('Edit') }}</a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger border-0">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-secondary">
                            {{ __('No pages found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">
            {{ $pages->links() }}
        </div>
    </div>
</div>
@endsection
