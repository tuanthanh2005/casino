@extends('layouts.admin')

@section('page_title', __('Dashboard'))

@section('admin_content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <div class="card" style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">{{ __('Total Posts') }}</span>
            <div style="font-size: 2rem; font-weight: 800; margin-top: 0.25rem;">{{ $stats['total_posts'] }}</div>
        </div>
        <div style="width: 48px; height: 48px; background: #e0f2fe; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">📝</div>
    </div>
    <div class="card" style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">{{ __('Published') }}</span>
            <div style="font-size: 2rem; font-weight: 800; margin-top: 0.25rem;">{{ $stats['published_posts'] }}</div>
        </div>
        <div style="width: 48px; height: 48px; background: #dcfce7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">✅</div>
    </div>
    <div class="card" style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">{{ __('Categories') }}</span>
            <div style="font-size: 2rem; font-weight: 800; margin-top: 0.25rem;">{{ $stats['total_categories'] }}</div>
        </div>
        <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">📂</div>
    </div>
    <div class="card" style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">{{ __('Featured') }}</span>
            <div style="font-size: 2rem; font-weight: 800; margin-top: 0.25rem;">{{ $stats['featured_posts'] }}</div>
        </div>
        <div style="width: 48px; height: 48px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">⭐</div>
    </div>
</div>

<div class="card" style="padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3 style="margin-bottom: 0;">{{ __('Recent Posts') }}</h3>
        <a href="/admin/posts" style="font-size: 0.875rem; font-weight: 600; color: var(--primary);">{{ __('View All') }}</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Published') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($latest_posts as $post)
            <tr>
                <td><span style="font-weight: 600; color: #0f172a;">{{ $post->title }}</span></td>
                <td>{{ $post->category->name }}</td>
                <td>
                    <span class="badge {{ $post->status === 'published' ? 'badge-published' : 'badge-draft' }}">
                        {{ __('' . $post->status) }}
                    </span>
                </td>
                <td>{{ $post->published_at?->format('M d, Y') ?? 'N/A' }}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/admin/posts/{{ $post->id }}/edit" class="btn" style="padding: 0.25rem 0.625rem; font-size: 0.75rem; background: #f1f5f9; color: #475569;">{{ __('Edit') }}</a>
                        <a href="/blog/{{ $post->slug }}" target="_blank" class="btn" style="padding: 0.25rem 0.625rem; font-size: 0.75rem; background: #e0f2fe; color: #0369a1;">{{ __('Preview') }}</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
