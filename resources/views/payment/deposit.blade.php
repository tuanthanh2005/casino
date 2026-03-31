@extends('layouts.app')
@section('title', 'Nạp Tiền')

@push('styles')
<style>
.pay-tabs { display:flex; gap:1rem; margin-bottom:1.5rem; }
.pay-tab  { flex:1; padding:1.25rem; border-radius:16px; border:2px solid var(--border); background:var(--bg-card); cursor:pointer; text-align:center; transition:all 0.2s; }
.pay-tab.active { border-color:var(--primary); background:rgba(99,102,241,0.08); }
.pay-tab .tab-icon { font-size:2rem; margin-bottom:0.4rem; }
.pay-tab .tab-title { font-weight:700; font-size:1rem; }
.pay-tab .tab-sub   { font-size:0.78rem; color:var(--text-muted); margin-top:0.2rem; }

.card-type-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem; }
.card-type-btn  { padding:0.75rem 0.5rem; border-radius:12px; border:2px solid var(--border); background:var(--bg-card2); cursor:pointer; text-align:center; font-size:0.8rem; font-weight:600; transition:all 0.2s; color:var(--text); font-family:'Inter',sans-serif; }
.card-type-btn.active, .card-type-btn:hover { border-color:var(--accent); color:var(--accent); }

.amount-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:0.5rem; }
.amount-preset { padding:0.6rem; border-radius:10px; border:1px solid var(--border); background:var(--bg-card2); cursor:pointer; text-align:center; font-size:0.82rem; font-weight:600; color:var(--text-muted); transition:all 0.2s; font-family:'Inter',sans-serif; }
.amount-preset:hover { border-color:var(--primary); color:var(--primary); }

.qr-box { text-align:center; padding:1.5rem; background:var(--bg-card2); border-radius:16px; border:1px solid var(--border); }
.qr-box img { border-radius:12px; max-width:240px; border:4px solid white; box-shadow:0 8px 30px rgba(0,0,0,0.3); }

.hist-table { width:100%; border-collapse:collapse; font-size:0.82rem; }
.hist-table th { padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border); }
.hist-table td { padding:0.65rem 0.75rem; border-bottom:1px solid rgba(255,255,255,0.04); }
</style>
@endpush

@section('content')
<div style="margin-bottom:1.5rem">
    <h1 style="font-size:1.75rem; font-weight:900">💳 Nạp Tiền</h1>
    <p style="color:var(--text-muted); margin-top:0.25rem">Nạp điểm vào tài khoản · 1 VNĐ = 1 PT</p>
</div>

{{-- TAB CHỌN PHƯƠNG THỨC --}}
<div class="pay-tabs">
    <div class="pay-tab active" id="tab-bank" onclick="switchTab('bank')">
        <div class="tab-icon">🏦</div>
        <div class="tab-title">Chuyển khoản QR</div>
        <div class="tab-sub">MB Bank · Tức thì</div>
    </div>
    <div class="pay-tab" id="tab-card" onclick="switchTab('card')">
        <div class="tab-icon">🎴</div>
        <div class="tab-title">Thẻ cào</div>
        <div class="tab-sub">Viettel · Vina · Mobi · Gmobile</div>
    </div>
</div>

{{-- ────────────────────── BANK QR ────────────────────── --}}
<div id="panel-bank">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start">
        {{-- FORM --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-qr-code"></i> Nạp qua QR Bank</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">Số tiền muốn nạp (VNĐ)</label>
                    <div class="amount-grid" style="margin-bottom:0.75rem">
                        @foreach([50000,100000,200000,500000,1000000,2000000] as $v)
                        <button class="amount-preset" onclick="setBankAmt({{$v}})">{{ number_format($v) }}đ</button>
                        @endforeach
                    </div>
                    <input type="number" id="bank-amount" class="form-control" placeholder="Hoặc nhập số tiền..." min="10000" step="1000"
                           oninput="calcBankPt()" style="margin-bottom:0.75rem">

                    {{-- LIVE CALC BOX --}}
                    <div id="bank-calc" style="display:none; background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(99,102,241,0.06)); border:1px solid rgba(99,102,241,0.3); border-radius:12px; padding:1rem; margin-bottom:0.75rem">
                        <div style="display:flex; flex-direction:column; gap:0.4rem; font-size:0.85rem">
                            <div style="display:flex; justify-content:space-between">
                                <span style="color:var(--text-muted)">Số tiền nạp:</span>
                                <strong id="bc-vnd" style="color:#f59e0b">—</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between">
                                <span style="color:var(--text-muted)">Tỷ lệ quy đổi:</span>
                                <span style="color:var(--text-muted)">1 VNĐ = 1 PT</span>
                            </div>
                            <hr style="border-color:rgba(99,102,241,0.2); margin:0.25rem 0">
                            <div style="display:flex; justify-content:space-between; font-size:1rem">
                                <strong>🎉 Bạn nhận được:</strong>
                                <strong id="bc-pt" style="color:#10b981; font-size:1.2rem">—</strong>
                            </div>
                        </div>
                    </div>

                    <div style="font-size:0.75rem; color:var(--text-muted)">Tối thiểu 10.000 VNĐ &nbsp;&middot;&nbsp; 1 VNĐ = 1 PT</div>
                </div>
                <button onclick="createBankOrder()" class="btn btn-primary w-100" style="height:46px; font-size:0.95rem; font-weight:800">
                    <i class="bi bi-qr-code-scan"></i> Tạo mã QR & đơn hàng
                </button>
            </div>
        </div>

        {{-- QR HIỂN THỊ --}}
        <div id="qr-section" style="display:none">
            <div class="qr-box">
                <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.75rem">Quét QR để chuyển khoản</div>
                <img id="qr-img" src="" alt="QR Code">
                <div style="margin-top:1rem; background:var(--bg-card); border-radius:10px; padding:0.75rem; text-align:left; font-size:0.82rem">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem">
                        <span style="color:var(--text-muted)">Ngân hàng:</span>
                        <strong>MB Bank</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem">
                        <span style="color:var(--text-muted)">Số TK:</span>
                        <strong>0783704196</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem">
                        <span style="color:var(--text-muted)">Chủ TK:</span>
                        <strong>TRAN THANH TUAN</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem">
                        <span style="color:var(--text-muted)">Số tiền:</span>
                        <strong id="qr-amount" style="color:var(--accent)">—</strong>
                    </div>
                    <hr style="border-color:var(--border); margin:0.5rem 0">
                    <div style="display:flex; justify-content:space-between">
                        <span style="color:var(--text-muted)">Nội dung CK:</span>
                        <strong id="qr-code" style="color:var(--primary); font-size:1rem; letter-spacing:1px">—</strong>
                    </div>
                </div>
                <div style="margin-top:0.75rem; font-size:0.75rem; color:#f59e0b">
                    ⚠️ Nhập đúng nội dung chuyển khoản để admin xác nhận tự động!
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ────────────────────── THẺ CÀO ────────────────────── --}}
<div id="panel-card" style="display:none">
    <div class="card" style="max-width:600px">
        <div class="card-header"><i class="bi bi-credit-card-2-front"></i> Nạp thẻ cào</div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:1rem">
            <div>
                <label class="form-label">Nhà mạng</label>
                <div class="card-type-grid">
                    @foreach(['viettel'=>'Viettel 🔴','vinaphone'=>'VinaPhone 🔵','mobifone'=>'MobiFone 🟢','gmobile'=>'Gmobile ⚫','vietnamobile'=>'Vtmobile 🟡','reddi'=>'Reddi 🟠'] as $k=>$v)
                    <button class="card-type-btn" onclick="selectCardType('{{$k}}',this)">{{ $v }}</button>
                    @endforeach
                </div>
                <input type="hidden" id="selected-card-type">
            </div>
            <div>
                <label class="form-label">Mệnh giá thẻ</label>
                <div class="amount-grid">
                    @foreach([10000,20000,50000,100000,200000,500000] as $v)
                    <button class="amount-preset" id="ca-{{$v}}" onclick="selectCardAmt('{{$v}}',this)">{{ number_format($v) }}đ</button>
                    @endforeach
                </div>
                <input type="hidden" id="selected-card-amount">
            </div>

            {{-- LIVE CALC CARD --}}
            <div id="card-calc" style="display:none; background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); border-radius:12px; padding:1rem">
                <div style="display:flex; flex-direction:column; gap:0.4rem; font-size:0.85rem">
                    <div style="display:flex; justify-content:space-between">
                        <span style="color:var(--text-muted)">Mệnh giá thẻ:</span>
                        <strong id="cc-face" style="color:#f59e0b">—</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between">
                        <span style="color:var(--text-muted)">Nhà mạng:</span>
                        <span id="cc-carrier" style="color:var(--text-muted)">Chưa chọn</span>
                    </div>
                    <hr style="border-color:rgba(245,158,11,0.2); margin:0.25rem 0">
                    <div style="display:flex; justify-content:space-between; font-size:1rem">
                        <strong>🎉 Dự kiến nhận:</strong>
                        <strong id="cc-pt" style="color:#10b981; font-size:1.2rem">—</strong>
                    </div>
                    <div style="font-size:0.72rem; color:var(--text-muted)">* Admin kiểm tra thẻ thực tế trước khi cấp điểm</div>
                </div>
            </div>
            <div>
                <label class="form-label">Serial thẻ</label>
                <input type="text" id="card-serial" class="form-control" placeholder="Nhập serial thẻ...">
            </div>
            <div>
                <label class="form-label">Mã thẻ (PIN)</label>
                <input type="text" id="card-pin" class="form-control" placeholder="Nhập mã thẻ...">
            </div>
            <div style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); border-radius:10px; padding:0.75rem; font-size:0.8rem; color:#f59e0b">
                ⚠️ Thẻ sẽ được admin kiểm tra thủ công. Vui lòng chờ 5–30 phút.
            </div>
            <button onclick="submitCard()" class="btn btn-primary w-100" style="height:46px; font-size:0.95rem; font-weight:800">
                <i class="bi bi-send"></i> Gửi thẻ nạp tiền
            </button>
        </div>
    </div>
</div>

{{-- LỊCH SỬ NẠP --}}
<div class="card" style="margin-top:2rem">
    <div class="card-header"><i class="bi bi-clock-history"></i> Lịch sử nạp tiền</div>
    <div style="max-height:380px; overflow-y:auto; scrollbar-width:thin">
        <table class="hist-table">
            <thead>
                <tr>
                    <th>Mã đơn</th><th>Phương thức</th><th>Số tiền</th><th>Xu credit</th><th>Trạng thái</th><th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td><code style="color:var(--primary); font-size:0.8rem">{{ $o->order_code }}</code></td>
                    <td>{{ $o->method_label }}</td>
                    <td>{{ number_format($o->amount, 0) }} đ</td>
                    <td style="color:{{ $o->status==='approved' ? '#10b981' : 'var(--text-muted)' }}; font-weight:600">
                        {{ $o->status==='approved' ? '+'.number_format($o->points_credited,0).' PT' : '—' }}
                    </td>
                    <td>
                        @if($o->status==='pending')
                            <span class="badge badge-warning">⏳ Chờ duyệt</span>
                        @elseif($o->status==='approved')
                            <span class="badge badge-success">✅ Đã duyệt</span>
                        @else
                            <span class="badge badge-danger">❌ Từ chối</span>
                        @endif
                    </td>
                    <td style="color:var(--text-muted); font-size:0.75rem">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-muted)">Chưa có giao dịch nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function vi(n) { return Number(n).toLocaleString('vi-VN'); }

/* ── TAB ── */
function switchTab(tab) {
    ['bank','card'].forEach(t => {
        document.getElementById(`tab-${t}`).classList.toggle('active', t === tab);
        document.getElementById(`panel-${t}`).style.display = t === tab ? '' : 'none';
    });
}

/* ── BANK: live calc ── */
function setBankAmt(v) {
    document.getElementById('bank-amount').value = v;
    calcBankPt();
}
function calcBankPt() {
    const vnd = parseFloat(document.getElementById('bank-amount').value) || 0;
    const box = document.getElementById('bank-calc');
    if (vnd >= 10000) {
        const pt = vnd; // 1:1
        document.getElementById('bc-vnd').textContent = vi(vnd) + ' đ';
        document.getElementById('bc-pt').textContent  = vi(pt) + ' PT';
        box.style.display = '';
        // animate
        box.style.transform = 'scale(1.02)';
        setTimeout(() => box.style.transform = '', 150);
    } else {
        box.style.display = 'none';
    }
}

/* ── BANK: create order ── */
async function createBankOrder() {
    const amount = parseFloat(document.getElementById('bank-amount').value);
    if (!amount || amount < 10000) { showToast('Nhập số tiền tối thiểu 10.000đ', 'error'); return; }

    const resp = await fetch('/payment/deposit', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({ method:'bank_qr', amount })
    });
    const data = await resp.json();
    if (!data.success) { showToast(data.message, 'error'); return; }

    document.getElementById('qr-img').src    = data.qr_url;
    document.getElementById('qr-code').textContent   = data.order_code;
    document.getElementById('qr-amount').textContent = Number(data.amount).toLocaleString('vi-VN') + ' đ';
    document.getElementById('qr-section').style.display = '';
    showToast(data.message, 'success');
    setTimeout(() => location.reload(), 30000);
}

/* ── CARD: live calc ── */
let selectedCardType = '', selectedCardAmt = '';
const carrierLabels = {
    viettel:'Viettel 🔴', vinaphone:'VinaPhone 🔵', mobifone:'MobiFone 🟢',
    gmobile:'Gmobile ⚫', vietnamobile:'Vtmobile 🟡', reddi:'Reddi 🟠'
};

function selectCardType(v, el) {
    selectedCardType = v;
    document.querySelectorAll('.card-type-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    updateCardCalc();
}
function selectCardAmt(v, el) {
    selectedCardAmt = v;
    document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    updateCardCalc();
}
function updateCardCalc() {
    const box = document.getElementById('card-calc');
    if (selectedCardAmt) {
        const face = parseInt(selectedCardAmt);
        document.getElementById('cc-face').textContent    = vi(face) + ' đ';
        document.getElementById('cc-carrier').textContent = selectedCardType ? carrierLabels[selectedCardType] : 'Chưa chọn';
        document.getElementById('cc-pt').textContent      = vi(face) + ' PT';
        box.style.display = '';
        box.style.transform = 'scale(1.02)';
        setTimeout(() => box.style.transform = '', 150);
    } else {
        box.style.display = 'none';
    }
}

/* ── CARD: submit ── */
async function submitCard() {
    if (!selectedCardType) { showToast('Chọn nhà mạng', 'error'); return; }
    if (!selectedCardAmt)  { showToast('Chọn mệnh giá', 'error'); return; }
    const serial = document.getElementById('card-serial').value.trim();
    const pin    = document.getElementById('card-pin').value.trim();
    if (!serial || !pin) { showToast('Nhập đầy đủ serial và mã thẻ', 'error'); return; }

    const resp = await fetch('/payment/deposit', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({ method:'card', card_type:selectedCardType, card_serial:serial, card_pin:pin, card_amount:selectedCardAmt })
    });
    const data = await resp.json();
    showToast(data.message, data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 2000);
}
</script>
@endpush
