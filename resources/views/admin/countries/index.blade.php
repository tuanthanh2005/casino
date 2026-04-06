@extends('layouts.admin')

@section('page_title', __('Country Management'))

@section('header_actions')
<a href="{{ route('admin.countries.create') }}" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 99px; background: #0f172a; border: none;">
    {{ __('Add New Country') }}
</a>
@endsection

@section('admin_content')
<div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-secondary small text-uppercase">{{ __('Icon') }}</th>
                    <th class="py-3 text-secondary small text-uppercase">{{ __('Country Name') }}</th>
                    <th class="py-3 text-secondary small text-uppercase text-center">{{ __('Code') }}</th>
                    <th class="py-3 text-secondary small text-uppercase text-center">{{ __('Status') }}</th>
                    <th class="pe-4 py-3 text-secondary small text-uppercase text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($countries as $country)
                <tr>
                    <td class="ps-4">
                        @if($country->icon)
                            <img src="{{ asset('uploads/countries/' . $country->icon) }}" alt="{{ $country->name }}" style="width: 32px; height: 20px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-secondary small fw-bold" style="width: 32px; height: 20px; border-radius: 4px;">?</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $country->name }}</div>
                    </td>
                    <td class="text-center">
                        <code class="small text-secondary">{{ strtoupper($country->code) }}</code>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $country->status ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.65rem;">
                            {{ $country->status ? __('ACTIVE') : __('INACTIVE') }}
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-sm btn-outline-dark border-0 bg-light rounded-3">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('admin.countries.destroy', $country) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
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
