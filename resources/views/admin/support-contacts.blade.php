@extends('layouts.admin')
@section('title', 'Liên hệ hỗ trợ')

@section('admin-content')
<div class="page-header">
    <h1>📞 Cấu hình liên hệ hỗ trợ</h1>
    <p>Quản lý nội dung modal Liên hệ ở phía người dùng.</p>
</div>

<div class="card" style="max-width:900px">
    <div class="card-header">
        <span><i class="bi bi-headset"></i> Thông tin hiển thị</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.support.contacts.save') }}" style="display:grid; gap:1rem">
            @csrf

            <div>
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="support_title" class="form-control" value="{{ old('support_title', $settings['support_title']) }}" required>
            </div>

            <div>
                <label class="form-label">Mô tả ngắn</label>
                <input type="text" name="support_subtitle" class="form-control" value="{{ old('support_subtitle', $settings['support_subtitle']) }}" required>
            </div>

            <div>
                <label class="form-label">Tên nút trung tâm hỗ trợ</label>
                <input type="text" name="support_center_label" class="form-control" value="{{ old('support_center_label', $settings['support_center_label']) }}" required>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem">
                <div>
                    <label class="form-label">Hotline</label>
                    <input type="text" name="support_phone" class="form-control" value="{{ old('support_phone', $settings['support_phone']) }}" required>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $settings['support_email']) }}" required>
                </div>
            </div>

            <div>
                <label class="form-label">Link Telegram</label>
                <input type="url" name="support_zalo_url" class="form-control" value="{{ old('support_zalo_url', $settings['support_zalo_url']) }}" required>
            </div>

            <div>
                <label class="form-label">Link Messenger</label>
                <input type="url" name="support_messenger_url" class="form-control" value="{{ old('support_messenger_url', $settings['support_messenger_url']) }}" required>
            </div>

            <div>
                <label class="form-label">Giờ hỗ trợ</label>
                <input type="text" name="support_working_hours" class="form-control" value="{{ old('support_working_hours', $settings['support_working_hours']) }}" required>
            </div>

            <div style="margin-top:0.4rem; padding-top:1rem; border-top:1px dashed var(--border)">
                <div style="font-weight:700; margin-bottom:0.75rem">Telegram thông báo đơn hàng</div>

                <label style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.75rem; font-size:0.9rem">
                    <input type="checkbox" name="telegram_enabled" value="1" {{ old('telegram_enabled', $settings['telegram_enabled']) == '1' ? 'checked' : '' }}>
                    <span>Bật gửi thông báo Telegram</span>
                </label>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem">
                    <div>
                        <label class="form-label">Telegram Bot Token</label>
                        <input type="text" name="telegram_bot_token" class="form-control" value="{{ old('telegram_bot_token', $settings['telegram_bot_token']) }}" placeholder="VD: 123456:ABC...">
                    </div>
                    <div>
                        <label class="form-label">Telegram Chat ID</label>
                        <input type="text" name="telegram_chat_id" class="form-control" value="{{ old('telegram_chat_id', $settings['telegram_chat_id']) }}" placeholder="VD: -100xxxxxxxxxx">
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:0.5rem">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu cấu hình
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
