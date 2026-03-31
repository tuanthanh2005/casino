@extends('layouts.app')
@section('title', 'Vòng Quay May Mắn')

@push('styles')
<style>
.spin-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
    align-items: start;
}
@media(max-width:900px){ .spin-layout{grid-template-columns:1fr} }

/* WHEEL */
.wheel-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 24px;
    position: relative;
}
.wheel-pointer {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 2rem;
    z-index: 10;
    filter: drop-shadow(0 4px 12px rgba(245,158,11,0.7));
    animation: pointer-pulse 1.5s ease-in-out infinite;
}
@keyframes pointer-pulse {
    0%,100%{ transform: translateX(-50%) translateY(0); }
    50%    { transform: translateX(-50%) translateY(-4px); }
}
#spin-canvas {
    border-radius: 50%;
    box-shadow: 0 0 0 6px rgba(99,102,241,0.3), 0 0 40px rgba(99,102,241,0.15);
    cursor: pointer;
    transition: box-shadow 0.3s;
}
#spin-canvas:hover {
    box-shadow: 0 0 0 6px rgba(245,158,11,0.5), 0 0 50px rgba(245,158,11,0.2);
}

/* RESULT OVERLAY */
#spin-result-overlay {
    display: none;
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.75);
    border-radius: 24px;
    align-items: center;
    justify-content: center;
    z-index: 20;
    flex-direction: column;
    gap: 0.5rem;
    animation: fadeIn 0.3s ease;
}
#spin-result-overlay.show { display: flex; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }

/* PRESET AMOUNTS */
.preset-btn {
    padding: 0.4rem 0.85rem;
    background: var(--bg-card2);
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 0.8rem;
    cursor: pointer;
    color: var(--text-muted);
    transition: all 0.15s;
    font-family: 'Inter', sans-serif;
}
.preset-btn:hover { border-color: var(--accent); color: var(--accent); }

/* PRIZES TABLE */
.prize-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.82rem;
    border: 1px solid transparent;
    transition: all 0.2s;
}
.prize-row.highlight {
    background: rgba(245,158,11,0.12);
    border-color: rgba(245,158,11,0.4);
    transform: scale(1.02);
}
</style>
@endpush

@section('content')
<div style="margin-bottom:1.5rem">
    <h1 style="font-size:1.75rem; font-weight:900">🎡 Vòng Quay May Mắn</h1>
    <p style="color:var(--text-muted); margin-top:0.25rem">Quay để nhân điểm — May mắn sẽ đến với bạn!</p>
</div>

<div class="spin-layout">
    <!-- LEFT: WHEEL -->
    <div class="wheel-wrap">
        <div class="wheel-pointer">▼</div>
        <canvas id="spin-canvas" width="380" height="380"></canvas>

        <!-- Result Overlay -->
        <div id="spin-result-overlay">
            <div id="spin-result-icon" style="font-size:3rem"></div>
            <div id="spin-result-title" style="font-size:1.5rem; font-weight:900"></div>
            <div id="spin-result-sub" style="color:var(--text-muted); font-size:0.9rem"></div>
            <button onclick="closeSpinResult()" style="
                margin-top:0.75rem; padding:0.6rem 1.5rem; background:var(--primary);
                color:white; border:none; border-radius:10px; font-weight:700;
                cursor:pointer; font-family:'Inter',sans-serif;
            ">Quay tiếp</button>
        </div>
    </div>

    <!-- RIGHT: CONTROL + ODDS -->
    <div style="display:flex; flex-direction:column; gap:1rem">
        <!-- Balance -->
        <div style="background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); border-radius:14px; padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between">
            <span style="color:var(--text-muted); font-size:0.875rem"><i class="bi bi-coin"></i> Số dư</span>
            <span style="font-weight:800; font-size:1.2rem; color:var(--accent)" id="spin-balance">{{ number_format((float)auth()->user()->balance_point, 2) }} PT</span>
        </div>

        <!-- Bet Input -->
        <div class="card">
            <div class="card-body" style="padding:1.25rem">
                <label class="form-label">Số Point đặt cược</label>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.75rem">
                    @foreach([10,50,100,500,1000] as $p)
                        <button class="preset-btn" onclick="document.getElementById('spin-amount').value={{$p}};updateSpinPreview()">{{ number_format($p) }}</button>
                    @endforeach
                    <button class="preset-btn" onclick="document.getElementById('spin-amount').value=Math.floor({{ (float)auth()->user()->balance_point }});updateSpinPreview()">MAX</button>
                </div>
                <input type="number" id="spin-amount" class="form-control" placeholder="Nhập số điểm..." min="1"
                       oninput="updateSpinPreview()" style="margin-bottom:0.75rem">
                <div style="background:var(--bg-card2); border-radius:10px; padding:0.75rem; font-size:0.8rem; color:var(--text-muted); margin-bottom:1rem">
                    🏆 Nếu thắng x5: <strong style="color:var(--accent)" id="spin-max-win">0 PT</strong>
                </div>
                <button id="spin-btn" onclick="doSpin()" class="btn btn-primary w-100" style="height:48px; font-size:1rem; font-weight:800">
                    🎡 QUAY NGAY
                </button>
            </div>
        </div>

        <!-- Odds Table -->
        <div class="card">
            <div class="card-header" style="font-size:0.875rem"><i class="bi bi-table"></i> Bảng xác suất</div>
            <div style="padding:0.75rem">
                @php
                $prizeList = [
                    ['label'=>'💀 Mất tất', 'mult'=>'x0', 'prob'=>'25%', 'color'=>'#6b7280'],
                    ['label'=>'😅 x0.5',   'mult'=>'x0.5','prob'=>'25%','color'=>'#a78bfa'],
                    ['label'=>'😐 Hoàn',   'mult'=>'x1',  'prob'=>'20%','color'=>'#60a5fa'],
                    ['label'=>'😊 x1.5',   'mult'=>'x1.5','prob'=>'15%','color'=>'#34d399'],
                    ['label'=>'🤩 x2',     'mult'=>'x2',  'prob'=>'10%','color'=>'#fbbf24'],
                    ['label'=>'🔥 x3',     'mult'=>'x3',  'prob'=>'4%', 'color'=>'#f87171'],
                    ['label'=>'💎 x5',     'mult'=>'x5',  'prob'=>'1%', 'color'=>'#c084fc'],
                ];
                @endphp
                @foreach($prizeList as $i => $pr)
                <div class="prize-row" id="prize-row-{{ $i }}">
                    <span>{{ $pr['label'] }}</span>
                    <div style="display:flex; gap:0.75rem; align-items:center">
                        <span style="color:{{ $pr['color'] }}; font-weight:700">{{ $pr['mult'] }}</span>
                        <span style="color:var(--text-muted)">{{ $pr['prob'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- LỊCH SỬ VÒNG QUAY --}}
<div style="margin-top:2rem">
    @php
        $thisMonth  = $history->filter(fn($h) => $h->created_at->isCurrentMonth());
        $thisWins   = $thisMonth->where('won', true)->count();
        $thisLoses  = $thisMonth->where('won', false)->count();
        $thisProfit = $thisMonth->sum('profit');
        $thisTotal  = $thisMonth->count();
    @endphp
    {{-- STATS THÁNG --}}
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:1rem; margin-bottom:1rem">
        @foreach([
            ['label'=>'Ván tháng này', 'val'=> $thisTotal, 'color'=>'#818cf8'],
            ['label'=>'Thắng', 'val'=> $thisWins, 'color'=>'#10b981'],
            ['label'=>'Thua', 'val'=> $thisLoses, 'color'=>'#ef4444'],
            ['label'=>'Win rate', 'val'=> ($thisTotal>0 ? round($thisWins/$thisTotal*100,1) : 0).'%', 'color'=> $thisTotal>0 && $thisWins/$thisTotal>0.5 ? '#f59e0b' : '#9ca3af'],
            ['label'=>'Profit tháng', 'val'=> ($thisProfit>=0?'+':'').number_format($thisProfit,0).' PT', 'color'=> $thisProfit>=0 ? '#10b981' : '#ef4444'],
        ] as $s)
        <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:14px; padding:1rem 1.25rem; text-align:center">
            <div style="font-size:0.72rem; color:var(--text-muted); margin-bottom:0.35rem">{{ $s['label'] }}</div>
            <div style="font-size:1.25rem; font-weight:900; color:{{ $s['color'] }}">{{ $s['val'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- BẢNG LỊCH SỬ --}}
    <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:16px; overflow:hidden">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border); font-weight:600; font-size:0.95rem; display:flex; align-items:center; justify-content:space-between">
            <span><i class="bi bi-clock-history"></i> Lịch sử Vòng Quay của bạn</span>
            <span style="font-size:0.72rem; color:var(--text-muted); font-weight:400">50 ván gần nhất · scroll để xem thêm</span>
        </div>
        <div style="max-height:400px; overflow-y:auto; scrollbar-width:thin; scrollbar-color:rgba(99,102,241,0.4) transparent">
            <table style="width:100%; border-collapse:collapse; font-size:0.82rem">
                <thead>
                    <tr style="position:sticky; top:0; background:var(--bg-card2); z-index:2">
                        <th style="padding:0.6rem 1rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">#</th>
                        <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Kết quả</th>
                        <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Hệ số</th>
                        <th style="padding:0.6rem 0.75rem; text-align:right; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Đặt</th>
                        <th style="padding:0.6rem 0.75rem; text-align:right; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Profit</th>
                        <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $h)
                    @php $d = $h->details ?? []; $mult = $d['mult'] ?? 0; @endphp
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04)">
                        <td style="padding:0.6rem 1rem; color:var(--text-muted)">{{ $loop->iteration }}</td>
                        <td style="padding:0.6rem 0.75rem">
                            @if($h->won)
                                <span style="color:#10b981; font-weight:700">✅ Thắng</span>
                            @elseif($mult > 0)
                                <span style="color:#f59e0b; font-weight:700">😅 Hoàn</span>
                            @else
                                <span style="color:#ef4444; font-weight:700">❌ Thua</span>
                            @endif
                        </td>
                        <td style="padding:0.6rem 0.75rem; font-weight:700; color:{{ $mult >= 2 ? '#a78bfa' : ($mult > 0 ? '#60a5fa' : '#6b7280') }}">
                            x{{ $mult }}
                        </td>
                        <td style="padding:0.6rem 0.75rem; text-align:right">{{ number_format((float)$h->bet_amount, 0) }} PT</td>
                        <td style="padding:0.6rem 0.75rem; text-align:right; font-weight:700">
                            @if($h->profit > 0)
                                <span style="color:#10b981">+{{ number_format((float)$h->profit, 0) }}</span>
                            @elseif($h->profit < 0)
                                <span style="color:#ef4444">{{ number_format((float)$h->profit, 0) }}</span>
                            @else
                                <span style="color:var(--text-muted)">0</span>
                            @endif
                        </td>
                        <td style="padding:0.6rem 0.75rem; color:var(--text-muted); font-size:0.75rem">{{ $h->created_at->format('d/m H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:3rem 1rem; color:var(--text-muted)">
                            <div style="font-size:2rem; margin-bottom:0.5rem">🎡</div>
                            Chưa có lịch sử. Hãy quay thử!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let isSpinning = false;
let currentAngle = 0;
let userBalance  = {{ (float)auth()->user()->balance_point }};

// Prize config (MUST match backend order)
const PRIZES = [
    { label: '💀 Mất tất', mult: 0,   color: '#374151' },
    { label: '😅 x0.5',   mult: 0.5,  color: '#6b21a8' },
    { label: '😐 x1',     mult: 1,    color: '#1e40af' },
    { label: '😊 x1.5',   mult: 1.5,  color: '#065f46' },
    { label: '🤩 x2',     mult: 2,    color: '#92400e' },
    { label: '🔥 x3',     mult: 3,    color: '#991b1b' },
    { label: '💎 x5',     mult: 5,    color: '#7c3aed' },
];
const N = PRIZES.length;
const SLICE = (Math.PI * 2) / N;

// Draw wheel
const canvas = document.getElementById('spin-canvas');
const ctx    = canvas.getContext('2d');
const CX = canvas.width / 2, CY = canvas.height / 2, R = CX - 10;

function drawWheel(angle) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Outer glow ring
    const grd = ctx.createRadialGradient(CX, CY, R - 4, CX, CY, R + 4);
    grd.addColorStop(0, 'rgba(99,102,241,0.6)');
    grd.addColorStop(1, 'rgba(99,102,241,0)');
    ctx.beginPath(); ctx.arc(CX, CY, R, 0, Math.PI * 2);
    ctx.strokeStyle = grd; ctx.lineWidth = 8; ctx.stroke();

    PRIZES.forEach((p, i) => {
        const start = angle + i * SLICE;
        const end   = start + SLICE;

        // Sector fill
        ctx.beginPath();
        ctx.moveTo(CX, CY);
        ctx.arc(CX, CY, R, start, end);
        ctx.closePath();
        ctx.fillStyle = p.color;
        ctx.fill();
        ctx.strokeStyle = 'rgba(255,255,255,0.08)';
        ctx.lineWidth = 2;
        ctx.stroke();

        // Label
        const midAngle = start + SLICE / 2;
        const tx = CX + (R * 0.65) * Math.cos(midAngle);
        const ty = CY + (R * 0.65) * Math.sin(midAngle);
        ctx.save();
        ctx.translate(tx, ty);
        ctx.rotate(midAngle + Math.PI / 2);
        ctx.fillStyle = '#fff';
        ctx.font = 'bold 12px Inter,sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(p.label, 0, 0);
        ctx.restore();
    });

    // Center hub
    ctx.beginPath(); ctx.arc(CX, CY, 28, 0, Math.PI * 2);
    const hub = ctx.createRadialGradient(CX, CY, 0, CX, CY, 28);
    hub.addColorStop(0, '#6366f1'); hub.addColorStop(1, '#4f46e5');
    ctx.fillStyle = hub; ctx.fill();
    ctx.strokeStyle = 'rgba(255,255,255,0.2)'; ctx.lineWidth = 2; ctx.stroke();
    ctx.fillStyle = '#fff'; ctx.font = 'bold 14px Inter,sans-serif'; ctx.textAlign = 'center';
    ctx.fillText('SPIN', CX, CY + 5);
}
drawWheel(0);

function updateSpinPreview() {
    const amt = parseFloat(document.getElementById('spin-amount').value) || 0;
    document.getElementById('spin-max-win').textContent = (amt * 5).toLocaleString('en-US', {minimumFractionDigits:0}) + ' PT';
}

async function doSpin() {
    if (isSpinning) return;
    const amount = parseFloat(document.getElementById('spin-amount').value);
    if (!amount || amount <= 0) { showToast('Nhập số điểm cần đặt', 'error'); return; }
    if (amount > userBalance)   { showToast('Số dư không đủ', 'error'); return; }

    isSpinning = true;
    document.getElementById('spin-btn').disabled = true;
    document.getElementById('spin-btn').textContent = '⏳ Đang quay...';

    const freshCsrf = document.querySelector('meta[name="csrf-token"]').content;
    const resp = await fetch('/api/spin', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': freshCsrf, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify({ bet_amount: amount }),
    });
    const data = await resp.json();

    if (!data.success) {
        showToast(data.message, 'error');
        isSpinning = false;
        document.getElementById('spin-btn').disabled = false;
        document.getElementById('spin-btn').textContent = '🎡 QUAY NGAY';
        return;
    }

    // Highlight winning prize row
    document.querySelectorAll('.prize-row').forEach((r,i) => r.classList.toggle('highlight', i === data.prize_index));

    // Calculate target angle: prize_index-th sector should end up at top (270° = -PI/2)
    const targetSector = data.prize_index;
    const sectorMid    = targetSector * SLICE + SLICE / 2;
    // We want sectorMid to sit at -PI/2 (top, where pointer is)
    // pointer is at -PI/2, so we need: currentAngle + targetSector * SLICE + SLICE/2 = -PI/2 + 2PI*k
    const extraSpins   = Math.PI * 2 * (5 + Math.floor(Math.random() * 3)); // 5-7 full spins
    const needed       = (-Math.PI / 2 - sectorMid + Math.PI * 2 * 100) % (Math.PI * 2);
    const finalAngle   = currentAngle - (currentAngle % (Math.PI * 2)) + needed + extraSpins;

    // Animate
    const startAngle = currentAngle;
    const duration   = 4000 + Math.random() * 1500;
    const startTime  = performance.now();

    function easeOut(t) { return 1 - Math.pow(1 - t, 3); }

    function animate(now) {
        const elapsed = now - startTime;
        const t       = Math.min(elapsed / duration, 1);
        const angle   = startAngle + (finalAngle - startAngle) * easeOut(t);
        currentAngle  = angle;
        drawWheel(angle);
        if (t < 1) {
            requestAnimationFrame(animate);
        } else {
            currentAngle = finalAngle;
            drawWheel(finalAngle);
            showSpinResult(data);
        }
    }
    requestAnimationFrame(animate);
}

function showSpinResult(data) {
    const isWin = data.profit >= 0;
    document.getElementById('spin-result-icon').textContent  = isWin ? '🎉' : '😢';
    document.getElementById('spin-result-title').textContent = data.prize_label;
    document.getElementById('spin-result-sub').textContent   = isWin
        ? `+${data.payout} PT nhận về!`
        : `Mất ${Math.abs(data.profit)} PT`;
    document.getElementById('spin-result-title').style.color = isWin ? '#10b981' : '#ef4444';
    document.getElementById('spin-result-overlay').classList.add('show');

    userBalance = parseFloat(data.new_balance.replace(/,/g,''));
    document.getElementById('spin-balance').textContent = data.new_balance + ' PT';
    updateNavBalance(data.new_balance);
    showToast(data.message, isWin ? 'success' : 'error');
}

function closeSpinResult() {
    document.getElementById('spin-result-overlay').classList.remove('show');
    isSpinning = false;
    document.getElementById('spin-btn').disabled = false;
    document.getElementById('spin-btn').textContent = '🎡 QUAY NGAY';
}
</script>
@endpush
