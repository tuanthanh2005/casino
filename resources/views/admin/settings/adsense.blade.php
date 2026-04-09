@extends('layouts.admin')

@section('page_title', 'Quảng cáo Adsense')

@section('admin_content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.settings.adsense') }}" method="POST">
            @csrf

            <div class="mb-4 form-check form-switch px-0">
                <div class="d-flex align-items-center">
                    <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="adsense_enabled" name="adsense_enabled" value="1" style="width: 3rem; height: 1.5rem;" {{ ($settings['adsense_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label h5 mb-0" for="adsense_enabled" style="font-weight: 600;">
                        Bật kiếm tiền (Hiển thị quảng cáo Adsense)
                    </label>
                </div>
                <div class="form-text mt-2 text-muted ms-5">
                    Nếu tắt, quảng cáo sẽ không hiển thị trên website giúp giao diện gọn gàng. Khi kênh đã được duyệt kiếm tiền, hãy bật nút này lên.
                </div>
            </div>

            <div class="mb-4">
                <label for="adsense_code" class="form-label" style="font-weight: 600;">Mã quảng cáo Adsense (Auto Ads Code / Header Code)</label>
                <textarea class="form-control" id="adsense_code" name="adsense_code" rows="8" placeholder="<script async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXX' crossorigin='anonymous'></script>">{{ $settings['adsense_code'] ?? '' }}</textarea>
                <div class="form-text">Dán mã script Adsense của bạn vào đây. Hệ thống sẽ tự động thêm vào thẻ &lt;head&gt; của trang web khi được bật.</div>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2" style="font-weight: 600; border-radius: 8px;">Lưu Cài Đặt</button>
        </form>
    </div>
</div>
@endsection
