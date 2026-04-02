@extends('layouts.app')
@section('title', 'Rút / Đổi Điểm')

@push('styles')
<style>
.pay-tabs { display:flex; gap:1rem; margin-bottom:1.5rem; }
.pay-tab  { flex:1; padding:1.25rem; border-radius:16px; border:2px solid var(--border); background:var(--bg-card); cursor:pointer; text-align:center; transition:all 0.2s; }
.pay-tab.active { border-color:var(--primary); background:rgba(99,102,241,0.08); }
.pay-tab .tab-icon { font-size:2rem; margin-bottom:0.4rem; }
.pay-tab .tab-title { font-weight:700; font-size:1rem; }
.pay-tab .tab-sub   { font-size:0.78rem; color:var(--text-muted); margin-top:0.2rem; }
.card-type-btn { padding:0.65rem 0.5rem; border-radius:12px; border:2px solid var(--border); background:var(--bg-card2); cursor:pointer; text-align:center; font-size:0.8rem; font-weight:600; transition:all 0.2s; color:var(--text); font-family:'Inter',sans-serif; }
.card-type-btn.active, .card-type-btn:hover { border-color:var(--accent); color:var(--accent); }
.amount-preset-sm { padding:0.45rem 0.5rem; border-radius:9px; border:1px solid var(--border); background:var(--bg-card2); cursor:pointer; text-align:center; font-size:0.78rem; font-weight:600; color:var(--text-muted); transition:all 0.15s; font-family:'Inter',sans-serif; }
.amount-preset-sm:hover, .amount-preset-sm.active { border-color:var(--primary); color:var(--primary); }
.hist-table { width:100%; border-collapse:collapse; font-size:0.82rem; }
.hist-table th { padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border); }
.hist-table td { padding:0.65rem 0.75rem; border-bottom:1px solid rgba(255,255,255,0.04); }

@media (max-width: 768px) {
    .pay-tabs { gap:0.6rem; margin-bottom:1rem; }
    .pay-tab { padding:0.9rem 0.65rem; border-radius:14px; }
    .pay-tab .tab-icon { font-size:1.5rem; }
    .pay-tab .tab-title { font-size:0.9rem; }
    .pay-tab .tab-sub { font-size:0.72rem; }

    #panel-bank .card,
    #panel-card .card { max-width:100% !important; }

    #panel-bank .card-body > div[style*="grid-template-columns:repeat(4,1fr)"],
    #panel-card .card-body > div[style*="grid-template-columns:repeat(4,1fr)"],
    #panel-card .card-body > div[style*="grid-template-columns:repeat(3,1fr)"] {
        grid-template-columns:repeat(2,1fr) !important;
    }
}
</style>
@endpush

@section('content')
<div style="margin-bottom:1.5rem">
    <h1 style="font-size:1.75rem; font-weight:900">💸 Rút / Đổi Điểm</h1>
    <p style="color:var(--text-muted); margin-top:0.25rem">
        Số dư hiện tại: <strong style="color:var(--accent)">{{ number_format($balance, 0) }} PT</strong>
        &nbsp;·&nbsp; Thuế rút: <span style="color:#ef4444; font-weight:700">2%</span>
    </p>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:0.6rem; margin-bottom:1rem">
    <a href="{{ route('payment.deposit') }}" class="btn btn-outline" style="justify-content:center; font-weight:700">
        <i class="bi bi-plus-circle"></i> Nạp tiền
    </a>
    <a href="{{ route('payment.withdraw') }}" class="btn" style="justify-content:center; background:rgba(6,182,212,0.15); border:1px solid rgba(6,182,212,0.45); color:var(--primary); font-weight:800">
        <i class="bi bi-arrow-up-right-circle"></i> Rút / Đổi
    </a>
</div>

{{-- RATE LIMIT BANNER --}}
@if(!$rate['allowed'])
<div id="rate-banner" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.35); border-radius:14px; padding:1.25rem 1.5rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem">
    <div style="font-size:2rem">⏳</div>
    <div style="flex:1">
        <div style="font-weight:700; color:#ef4444; font-size:1rem">Quá giới hạn giao dịch</div>
        <div style="color:var(--text-muted); font-size:0.85rem; margin-top:0.2rem">
            Bạn đã giao dịch 5 lần trong 30 phút. Vui lòng chờ:
        </div>
    </div>
    <div style="text-align:center; background:rgba(239,68,68,0.15); border-radius:12px; padding:0.75rem 1.25rem; min-width:100px">
        <div id="rate-countdown" style="font-size:1.75rem; font-weight:900; color:#ef4444; font-family:monospace">{{ sprintf('%02d:%02d', floor($rate['retry_secs']/60), $rate['retry_secs']%60) }}</div>
        <div style="font-size:0.7rem; color:var(--text-muted)">còn lại</div>
    </div>
</div>
<script>
(function(){
    let s = {{ $rate['retry_secs'] }};
    const el = document.getElementById('rate-countdown');
    const iv = setInterval(() => {
        s--;
        if (s <= 0) { clearInterval(iv); location.reload(); return; }
        el.textContent = String(Math.floor(s/60)).padStart(2,'0')+':'+String(s%60).padStart(2,'0');
    }, 1000);
})();
</script>
@else
<div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.25); border-radius:12px; padding:0.75rem 1.25rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem; font-size:0.85rem">
    <i class="bi bi-shield-check" style="color:#10b981"></i>
    <span style="color:#10b981; font-weight:600">Còn {{ $rate['remaining'] }}/5 lần giao dịch trong 30 phút này</span>
</div>
@endif

{{-- TAB --}}
<div class="pay-tabs">
    <div class="pay-tab active" id="tab-bank" onclick="switchTab('bank')">
        <div class="tab-icon">🏦</div>
        <div class="tab-title">Chuyển khoản ngân hàng</div>
        <div class="tab-sub">Rút xu thành tiền mặt</div>
    </div>
    <div class="pay-tab" id="tab-card" onclick="switchTab('card')">
        <div class="tab-icon">🎴</div>
        <div class="tab-title">Đổi thành thẻ cào</div>
        <div class="tab-sub">Viettel · Vina · Mobi...</div>
    </div>
</div>

{{-- ─── BANK TRANSFER ─── --}}
<div id="panel-bank">
    <div class="card" style="max-width:600px">
        <div class="card-header"><i class="bi bi-bank"></i> Rút về tài khoản ngân hàng</div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:1rem">
            <div>
                <label class="form-label">Số PT muốn rút</label>
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0.5rem; margin-bottom:0.75rem">
                    @foreach([10000,50000,100000,500000] as $v)
                    <button class="amount-preset-sm" onclick="setBankPts({{$v}})">{{ number_format($v) }}</button>
                    @endforeach
                </div>
                <input type="number" id="bank-points" class="form-control" placeholder="Hoặc nhập số điểm..."
                       min="10000" step="1000" oninput="calcTax('bank')">
            </div>

            {{-- LIVE TAX BANK --}}
            <div id="bank-tax-box" style="display:none; background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(99,102,241,0.06)); border:1px solid rgba(99,102,241,0.35); border-radius:14px; padding:1.1rem; transition:all 0.3s">
                <div style="display:flex; flex-direction:column; gap:0.55rem; font-size:0.875rem">
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <span style="color:var(--text-muted)">Xu rút:</span>
                        <strong id="bank-tax-pts" style="color:var(--text)">—</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <span style="color:var(--text-muted)">Thuế 2%:</span>
                        <span id="bank-tax-fee" style="color:#ef4444; font-weight:600">—</span>
                    </div>
                    <div style="height:1px; background:rgba(99,102,241,0.25); margin:0.1rem 0"></div>
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <strong style="font-size:0.95rem">💸 Thực nhận (VNĐ):</strong>
                        <strong id="bank-tax-net" style="color:#10b981; font-size:1.35rem">—</strong>
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label">Ngân hàng</label>
                <input type="text" id="bk-bank-name" class="form-control" placeholder="VD: MB Bank, Vietcombank...">
            </div>
            <div>
                <label class="form-label">Số tài khoản</label>
                <input type="text" id="bk-bank-acc" class="form-control" placeholder="Nhập số tài khoản...">
            </div>
            <div>
                <label class="form-label">Tên chủ tài khoản</label>
                <input type="text" id="bk-bank-holder" class="form-control" placeholder="Nhập tên in hoa như trên thẻ...">
            </div>
            <button onclick="submitBank()" class="btn btn-primary w-100" style="height:46px; font-size:0.95rem; font-weight:800">
                <i class="bi bi-send"></i> Gửi yêu cầu rút tiền
            </button>
        </div>
    </div>
</div>

{{-- ─── ĐỔI THẺ CÀO ─── --}}
<div id="panel-card" style="display:none">
    <div class="card" style="max-width:600px">
        <div class="card-header"><i class="bi bi-credit-card-2-front"></i> Đổi xu thành thẻ cào</div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:1rem">
            <div>
                <label class="form-label">Loại thẻ muốn nhận</label>
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.6rem">
                    @foreach(['viettel'=>'Viettel 🔴','vinaphone'=>'VinaPhone 🔵','mobifone'=>'MobiFone 🟢','gmobile'=>'Gmobile ⚫','vietnamobile'=>'Vtmobile 🟡','reddi'=>'Reddi 🟠'] as $k=>$v)
                    <button class="card-type-btn" onclick="selectCardType('{{$k}}',this)">{{ $v }}</button>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="form-label">Số PT muốn đổi</label>
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0.5rem; margin-bottom:0.75rem">
                    @foreach([10000,50000,100000,500000] as $v)
                    <button class="amount-preset-sm" onclick="setCardPts({{$v}})">{{ number_format($v) }}</button>
                    @endforeach
                </div>
                <input type="number" id="card-points" class="form-control" placeholder="Hoặc nhập số điểm..."
                       min="10000" step="10000" oninput="calcTax('card')">
            </div>

            {{-- LIVE TAX CARD --}}
            <div id="card-tax-box" style="display:none; background:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(245,158,11,0.06)); border:1px solid rgba(245,158,11,0.35); border-radius:14px; padding:1.1rem; transition:all 0.3s">
                <div style="display:flex; flex-direction:column; gap:0.55rem; font-size:0.875rem">
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <span style="color:var(--text-muted)">Xu đổi:</span>
                        <strong id="card-tax-pts" style="color:var(--text)">—</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <span style="color:var(--text-muted)">Thuế 2%:</span>
                        <span id="card-tax-fee" style="color:#ef4444; font-weight:600">—</span>
                    </div>
                    <div style="height:1px; background:rgba(245,158,11,0.25); margin:0.1rem 0"></div>
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <strong style="font-size:0.95rem">🎟️ Giá trị thẻ nhận:</strong>
                        <strong id="card-tax-net" style="color:#10b981; font-size:1.35rem">—</strong>
                    </div>
                </div>
            </div>

            <div style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); border-radius:10px; padding:0.75rem; font-size:0.8rem; color:#f59e0b">
                ⚠️ Admin sẽ gửi mã thẻ cào qua tin nhắn / thông báo trong vòng 24h.
            </div>
            <button onclick="submitCard()" class="btn btn-warning w-100" style="height:46px; font-size:0.95rem; font-weight:800; color:#000">
                <i class="bi bi-gift"></i> Gửi yêu cầu đổi thẻ
            </button>
        </div>
    </div>
</div>

{{-- LỊCH SỬ RÚT --}}
<div class="card" style="margin-top:2rem">
    <div class="card-header"><i class="bi bi-clock-history"></i> Lịch sử rút / đổi</div>
    <div style="max-height:380px; overflow-y:auto; scrollbar-width:thin">
        <table class="hist-table">
            <thead>
                <tr>
                    <th>Mã đơn</th><th>Phương thức</th><th>Điểm rút</th><th>Thuế 2%</th><th>Thực nhận</th><th>Trạng thái</th><th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td><code style="color:var(--primary); font-size:0.8rem">{{ $o->order_code }}</code></td>
                    <td>{{ $o->method_label }}</td>
                    <td>{{ number_format($o->points_used, 0) }} PT</td>
                    <td style="color:#ef4444">-{{ number_format($o->tax_amount, 0) }}</td>
                    <td style="font-weight:700; color:#10b981">{{ number_format($o->net_amount, 0) }}</td>
                    <td>
                        @if($o->status==='pending')
                            <span class="badge badge-warning">⏳ Đang xử lý</span>
                        @elseif($o->status==='approved')
                            <span class="badge badge-success">✅ Hoàn thành</span>
                        @else
                            <span class="badge badge-danger">❌ Từ chối</span>
                        @endif
                    </td>
                    <td style="color:var(--text-muted); font-size:0.75rem">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted)">Chưa có giao dịch nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const MAX_PTS = {{ $balance }};
const TAX = 0.02;

function vi(n) { return Math.floor(n).toLocaleString('vi-VN'); }

/* ── TAB ── */
function switchTab(tab) {
    ['bank','card'].forEach(t => {
        document.getElementById(`tab-${t}`).classList.toggle('active', t === tab);
        document.getElementById(`panel-${t}`).style.display = t === tab ? '' : 'none';
    });
}

/* ── QUICK PRESETS ── */
function setBankPts(v) {
    document.getElementById('bank-points').value = v;
    calcTax('bank');
}
function setCardPts(v) {
    document.getElementById('card-points').value = v;
    calcTax('card');
}

/* ── LIVE TAX CALC ── */
function calcTax(type) {
    const inputEl = type === 'bank' ? 'bank-points' : 'card-points';
    const pts = parseFloat(document.getElementById(inputEl).value) || 0;
    const fee = pts * TAX;
    const net = pts - fee;
    const box = document.getElementById(`${type}-tax-box`);
    if (pts >= 10000) {
        document.getElementById(`${type}-tax-pts`).textContent = vi(pts) + ' PT';
        document.getElementById(`${type}-tax-fee`).textContent = '−' + vi(fee) + ' PT';
        document.getElementById(`${type}-tax-net`).textContent = vi(net) + (type === 'bank' ? ' VNĐ' : ' đ');
        box.style.display = '';
        // pulse animation
        box.style.transform = 'scale(1.01)';
        setTimeout(() => box.style.transform = '', 200);
    } else {
        box.style.display = 'none';
    }
}

/* ── CARD TYPE SELECT ── */
let selectedCardType = '';
function selectCardType(v, el) {
    selectedCardType = v;
    document.querySelectorAll('.card-type-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
}

/* ── SUBMIT BANK ── */
async function submitBank() {
    const pts = parseFloat(document.getElementById('bank-points').value);
    const bn  = document.getElementById('bk-bank-name').value.trim();
    const ba  = document.getElementById('bk-bank-acc').value.trim();
    const bh  = document.getElementById('bk-bank-holder').value.trim();
    if (!pts || pts < 10000) { showToast('Nhập tối thiểu 10.000 PT', 'error'); return; }
    if (pts > MAX_PTS)       { showToast('Số dư không đủ!', 'error'); return; }
    if (!bn || !ba || !bh)   { showToast('Điền đầy đủ thông tin ngân hàng', 'error'); return; }

    const resp = await fetch('/payment/withdraw', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({ method:'bank_transfer', points:pts, bank_name:bn, bank_account:ba, bank_holder:bh })
    });
    const data = await resp.json();
    if (data.rate_limit) { showToast(data.message, 'error'); setTimeout(() => location.reload(), 2000); return; }
    showToast(data.message, data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 2000);
}

/* ── SUBMIT CARD ── */
async function submitCard() {
    const pts = parseFloat(document.getElementById('card-points').value);
    if (!selectedCardType)   { showToast('Chọn loại thẻ muốn nhận', 'error'); return; }
    if (!pts || pts < 10000) { showToast('Nhập tối thiểu 10.000 PT', 'error'); return; }
    if (pts > MAX_PTS)       { showToast('Số dư không đủ!', 'error'); return; }

    const resp = await fetch('/payment/withdraw', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({ method:'card', points:pts, card_type:selectedCardType })
    });
    const data = await resp.json();
    if (data.rate_limit) { showToast(data.message, 'error'); setTimeout(() => location.reload(), 2000); return; }
    showToast(data.message, data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 2000);
}
</script>
@endpush
