@extends('layouts.app')
@section('title', 'Tài Xỉu')

@push('styles')
<style>
.dice-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
    align-items: start;
}
@media(max-width:900px){ .dice-layout{grid-template-columns:1fr} }

/* DICE DISPLAY */
.dice-arena {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 2.5rem 2rem;
    text-align: center;
    min-height: 380px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    position: relative;
}
.dice-container {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    align-items: center;
}
.die {
    width: 90px;
    height: 90px;
    background: linear-gradient(145deg, #f8fafc, #e2e8f0);
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.35), inset 0 1px 3px rgba(255,255,255,0.6);
    transition: transform 0.15s;
    display: block;
}
.die.rolling {
    animation: diceRoll 0.15s ease-in-out infinite;
}
@keyframes diceRoll {
    0%   { transform: rotate(0deg) scale(1); }
    25%  { transform: rotate(-8deg) scale(1.05); }
    50%  { transform: rotate(6deg) scale(0.95); }
    75%  { transform: rotate(-4deg) scale(1.05); }
    100% { transform: rotate(0deg) scale(1); }
}
.die-total {
    font-size: 3rem;
    font-weight: 900;
    transition: all 0.3s;
}
.result-banner {
    padding: 1rem 2rem;
    border-radius: 16px;
    font-size: 1.3rem;
    font-weight: 800;
    display: none;
    animation: popIn 0.4s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes popIn {
    from { transform: scale(0.5); opacity:0; }
    to   { transform: scale(1);   opacity:1; }
}

/* BET BUTTONS */
.bet-type-btns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}
.btn-tai {
    padding: 1rem;
    background: linear-gradient(135deg,rgba(239,68,68,0.15),rgba(239,68,68,0.05));
    border: 2px solid rgba(239,68,68,0.4);
    color: #ef4444;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    font-family: 'Inter',sans-serif;
}
.btn-tai:hover, .btn-tai.active {
    background: linear-gradient(135deg,#dc2626,#ef4444);
    border-color: #ef4444;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239,68,68,0.4);
}
.btn-xiu {
    padding: 1rem;
    background: linear-gradient(135deg,rgba(59,130,246,0.15),rgba(59,130,246,0.05));
    border: 2px solid rgba(59,130,246,0.4);
    color: #60a5fa;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    font-family: 'Inter',sans-serif;
}
.btn-xiu:hover, .btn-xiu.active {
    background: linear-gradient(135deg,#1d4ed8,#3b82f6);
    border-color: #3b82f6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59,130,246,0.4);
}

.preset-btn{padding:0.4rem 0.85rem;background:var(--bg-card2);border:1px solid var(--border);border-radius:8px;font-size:0.8rem;cursor:pointer;color:var(--text-muted);transition:all 0.15s;font-family:'Inter',sans-serif}
.preset-btn:hover{border-color:var(--primary);color:var(--primary)}

/* ── MOBILE APP STYLE ── */
@media (max-width: 768px) {
    .dice-layout {
        display: block !important;
        padding: 0 !important;
    }
    .dice-arena {
        padding: 1rem !important;
        min-height: 250px !important;
        background: transparent !important;
        border: none !important;
        margin-bottom: 0.5rem !important;
    }
    .die {
        width: 70px !important;
        height: 70px !important;
    }
    .die-total { font-size: 2rem !important; }
    .bet-type-btns { gap: 0.5rem !important; }
    .btn-tai, .btn-xiu { padding: 0.75rem 0.5rem !important; font-size: 0.9rem !important; }
    .desktop-only { display: none !important; }
    .card-header { display: none !important; }
    #dice-roll-btn { height: 55px !important; font-size: 1rem !important; }
}
</style>
@endpush

@section('content')
<div class="page-enter">
    <div class="desktop-only" style="margin-bottom:1.5rem">
        <h1 style="font-size:1.75rem; font-weight:900">🎲 Tài Xỉu</h1>
        <p style="color:var(--text-muted); margin-top:0.25rem">3 xúc xắc — Tổng ≥ 11 là TÀI, ≤ 10 là XỈU · Thắng x1.95</p>
    </div>

<div class="dice-layout">
    <!-- LEFT: ARENA -->
    <div class="dice-arena">
        <div style="color:var(--text-muted); font-size:0.875rem" id="dice-prompt">Chọn cửa và đặt cược để bắt đầu</div>

        <div class="dice-container">
            <canvas class="die" id="die-1" width="90" height="90"></canvas>
            <canvas class="die" id="die-2" width="90" height="90"></canvas>
            <canvas class="die" id="die-3" width="90" height="90"></canvas>
        </div>

        <div>
            <div style="color:var(--text-muted); font-size:0.8rem; margin-bottom:0.25rem; text-align:center">TỔNG</div>
            <div class="die-total" id="dice-total" style="color:var(--text-muted)">?</div>
        </div>

        <div class="result-banner" id="result-banner"></div>
    </div>

    <!-- RIGHT: CONTROLS -->
    <div style="display:flex; flex-direction:column; gap:1rem">
        <!-- Balance -->
        <div style="background:linear-gradient(135deg,rgba(99,102,241,0.1),rgba(99,102,241,0.05)); border:1px solid rgba(99,102,241,0.3); border-radius:14px; padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between">
            <span style="color:var(--text-muted); font-size:0.875rem"><i class="bi bi-coin"></i> Số dư</span>
            <span style="font-weight:800; font-size:1.2rem; color:var(--accent)" id="dice-balance">{{ number_format((float)auth()->user()->balance_point, 2) }} PT</span>
        </div>

        <!-- Bet Controls -->
        <div class="card">
            <div class="card-body" style="padding:1.25rem">
                <!-- Chọn cửa -->
                <label class="form-label">Chọn cửa</label>
                <div class="bet-type-btns" style="margin-bottom:1rem">
                    <button class="btn-tai" id="btn-tai" onclick="selectDice('tai')">
                        🔴 TÀI<br>
                        <span style="font-size:0.7rem; opacity:0.8">Tổng ≥ 11</span>
                    </button>
                    <button class="btn-xiu" id="btn-xiu" onclick="selectDice('xiu')">
                        🔵 XỈU<br>
                        <span style="font-size:0.7rem; opacity:0.8">Tổng ≤ 10</span>
                    </button>
                </div>

                <!-- Số điểm -->
                <label class="form-label">Số Point đặt cược</label>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.75rem">
                    @foreach([10,50,100,500,1000] as $p)
                        <button class="preset-btn" onclick="document.getElementById('dice-amount').value={{$p}};updateDicePreview()">{{ number_format($p) }}</button>
                    @endforeach
                    <button class="preset-btn" onclick="document.getElementById('dice-amount').value=Math.floor({{ (float)auth()->user()->balance_point }});updateDicePreview()">MAX</button>
                </div>
                <input type="number" id="dice-amount" class="form-control" placeholder="Nhập số điểm..."
                       min="1" oninput="updateDicePreview()" style="margin-bottom:0.75rem">

                <div style="background:var(--bg-card2); border-radius:10px; padding:0.75rem; font-size:0.8rem; color:var(--text-muted); margin-bottom:1rem">
                    🏆 Thắng nhận về: <strong style="color:var(--accent)" id="dice-win-preview">0 PT</strong>
                    <span style="opacity:0.6">(x1.95)</span>
                </div>

                <button id="dice-roll-btn" class="btn btn-primary w-100" style="height:48px; font-size:1rem; font-weight:800" onclick="rollDice()" disabled>
                    Chọn TÀI hoặc XỈU để bắt đầu
                </button>
            </div>
        </div>

        <!-- Rules -->
        <div class="card">
            <div class="card-header" style="font-size:0.875rem"><i class="bi bi-info-circle"></i> Luật chơi</div>
            <div style="padding:1rem 1.25rem; font-size:0.82rem; color:var(--text-muted); line-height:1.8">
                <div>🎲 <strong style="color:var(--text)">3 xúc xắc</strong> được tung mỗi ván</div>
                <div>🔴 <strong style="color:#ef4444">TÀI</strong> — Tổng 3 xúc xắc từ <strong>11 → 18</strong></div>
                <div>🔵 <strong style="color:#60a5fa">XỈU</strong> — Tổng 3 xúc xắc từ <strong>3 → 10</strong></div>
                <div>😱 <strong style="color:#f59e0b">Bão (três)</strong> — 3 mặt giống nhau → Banker thắng tất cả</div>
                <div style="margin-top:0.5rem">💰 Thắng nhận <strong style="color:var(--accent)">x1.95</strong> tiền cược</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════ --}}
{{-- LỊCH SỬ CỬA THÁNG --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div style="margin-top:2rem">
    {{-- THỐNG KÊ TÓM TẮT --}}
    @php
        $thisMonth   = $history->filter(fn($h) => $h->created_at->isCurrentMonth());
        $thisWins    = $thisMonth->where('won', true)->count();
        $thisLoses   = $thisMonth->where('won', false)->count();
        $thisProfit  = $thisMonth->sum('profit');
        $thisTotal   = $thisMonth->count();
        $thisBet     = $thisMonth->sum('bet_amount');
    @endphp
    <div class="desktop-only" style="display:grid; grid-template-columns:repeat(5,1fr); gap:1rem; margin-bottom:1rem">
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

    <!-- Mobile Mini Stats -->
    <div class="stats-grid-mobile @media(min-width:769px){display:none !important}" style="display:grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-bottom:1rem">
        <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:0.75rem; text-align:center">
            <div style="font-size:0.6rem; color:var(--text-muted)">Thắng tháng</div>
            <div style="font-weight:800; color:#10b981">{{ $thisWins }}</div>
        </div>
        <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:0.75rem; text-align:center">
            <div style="font-size:0.6rem; color:var(--text-muted)">Win Rate</div>
            <div style="font-weight:800; color:#f59e0b">{{ ($thisTotal>0 ? round($thisWins/$thisTotal*100,0) : 0) }}%</div>
        </div>
    </div>

    {{-- BẢNG LỊCH SỬ --}}
    <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:16px; overflow:hidden">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border); font-weight:600; font-size:0.95rem; display:flex; align-items:center; justify-content:space-between">
            <span><i class="bi bi-clock-history"></i> Lịch sử Tài Xỉu của bạn</span>
            <span style="font-size:0.72rem; color:var(--text-muted); font-weight:400">50 ván gần nhất · scroll để xem thêm</span>
        </div>
        <div style="max-height:400px; overflow-y:auto; scrollbar-width:thin; scrollbar-color:rgba(59,130,246,0.4) transparent">
            <table style="width:100%; border-collapse:collapse; font-size:0.82rem">
                <thead>
                    <tr style="position:sticky; top:0; background:var(--bg-card2); z-index:2">
                        <th style="padding:0.6rem 1rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">#</th>
                        <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Cửa đặt</th>
                        <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Kết quả</th>
                        <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Xúc xắc</th>
                        <th style="padding:0.6rem 0.75rem; text-align:right; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Đặt</th>
                        <th style="padding:0.6rem 0.75rem; text-align:right; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Profit</th>
                        <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border)">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $h)
                    @php
                        $d      = $h->details ?? [];
                        $dice   = $d['dice'] ?? [];
                        $total  = $d['total'] ?? '?';
                        $res    = $d['result'] ?? '';
                        $tri    = $d['triplet'] ?? false;
                        $faces  = ['','⚀','⚁','⚂','⚃','⚄','⚅'];
                    @endphp
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04)">
                        <td style="padding:0.6rem 1rem; color:var(--text-muted)">{{ $loop->iteration }}</td>
                        <td style="padding:0.6rem 0.75rem">
                            @if(isset($d['bet_type']) && $d['bet_type']==='tai')
                                <span style="color:#ef4444; font-weight:700">🔴 TÀI</span>
                            @elseif(isset($d['bet_type']) && $d['bet_type']==='xiu')
                                <span style="color:#60a5fa; font-weight:700">🔵 XỈU</span>
                            @else
                                <span style="color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td style="padding:0.6rem 0.75rem">
                            @if($tri)
                                <span style="color:#f59e0b; font-weight:700">😱 Bão</span>
                            @elseif($h->won)
                                <span style="color:#10b981; font-weight:700">✅ Thắng</span>
                            @else
                                <span style="color:#ef4444; font-weight:700">❌ Thua</span>
                            @endif
                            @if($res==='tai') <span style="color:#ef4444; font-size:0.72rem">(TÀI·{{ $total }})</span>
                            @elseif($res==='xiu') <span style="color:#60a5fa; font-size:0.72rem">(XỈU·{{ $total }})</span>
                            @endif
                        </td>
                        <td style="padding:0.6rem 0.75rem; font-size:1.1rem; letter-spacing:2px">
                            @foreach($dice as $dv) {{ $faces[$dv] ?? $dv }} @endforeach
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
                        <td colspan="7" style="text-align:center; padding:3rem 1rem; color:var(--text-muted)">
                            <div style="font-size:2rem; margin-bottom:0.5rem">🎲</div>
                            Chưa có lịch sử. Hãy chơi Tài Xỉu!
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

// Dot patterns for each die face [x%, y%]
const DOT_POSITIONS = {
    1: [[50,50]],
    2: [[28,28],[72,72]],
    3: [[28,28],[50,50],[72,72]],
    4: [[28,28],[72,28],[28,72],[72,72]],
    5: [[28,28],[72,28],[50,50],[28,72],[72,72]],
    6: [[28,25],[72,25],[28,50],[72,50],[28,75],[72,75]],
};

function renderDie(canvasId, value, color = '#1e293b') {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = canvas.width, H = canvas.height;
    ctx.clearRect(0, 0, W, H);

    // Die face background
    ctx.fillStyle = '#f8fafc';
    const r = 14;
    ctx.beginPath();
    ctx.moveTo(r, 0); ctx.lineTo(W-r, 0);
    ctx.quadraticCurveTo(W, 0, W, r);
    ctx.lineTo(W, H-r); ctx.quadraticCurveTo(W, H, W-r, H);
    ctx.lineTo(r, H); ctx.quadraticCurveTo(0, H, 0, H-r);
    ctx.lineTo(0, r); ctx.quadraticCurveTo(0, 0, r, 0);
    ctx.closePath();
    ctx.fill();

    if (!value) return; // empty (initial state)

    // Draw dots
    const dots = DOT_POSITIONS[value] || [];
    dots.forEach(([px, py]) => {
        ctx.beginPath();
        ctx.arc(W * px / 100, H * py / 100, 7, 0, Math.PI * 2);
        ctx.fillStyle = color;
        ctx.fill();
    });
}

function renderDieRolling(canvasId) {
    renderDie(canvasId, Math.floor(Math.random() * 6) + 1);
}

// Init: draw empty dice
['die-1','die-2','die-3'].forEach(id => renderDie(id, null));

let selectedDiceType = null;
let isRolling = false;
let userBalance = {{ (float)auth()->user()->balance_point }};

function selectDice(type) {
    selectedDiceType = type;
    document.getElementById('btn-tai').classList.toggle('active', type === 'tai');
    document.getElementById('btn-xiu').classList.toggle('active', type === 'xiu');
    updateDiceBtn();
}

function updateDicePreview() {
    const amt = parseFloat(document.getElementById('dice-amount').value) || 0;
    document.getElementById('dice-win-preview').textContent = (amt * 1.95).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' PT';
    updateDiceBtn();
}

function updateDiceBtn() {
    const amt = parseFloat(document.getElementById('dice-amount').value) || 0;
    const btn = document.getElementById('dice-roll-btn');
    if (!selectedDiceType) {
        btn.disabled = true;
        btn.textContent = 'Chọn TÀI hoặc XỈU để bắt đầu';
        return;
    }
    if (amt <= 0 || amt > userBalance) {
        btn.disabled = true;
        btn.textContent = amt > userBalance ? 'Không đủ Point' : 'Nhập số Point';
        return;
    }
    btn.disabled = false;
    btn.textContent = `🎲 TUNG XÚC XẮC — ${selectedDiceType === 'tai' ? '🔴 TÀI' : '🔵 XỈU'}`;
}

async function rollDice() {
    if (isRolling || !selectedDiceType) return;
    const amount = parseFloat(document.getElementById('dice-amount').value);
    if (!amount || amount <= 0) { showToast('Nhập số điểm cần đặt', 'error'); return; }
    if (amount > userBalance)   { showToast('Số dư không đủ', 'error'); return; }

    isRolling = true;
    const btn = document.getElementById('dice-roll-btn');
    btn.disabled = true;
    btn.textContent = '🎲 Đang tung...';
    document.getElementById('result-banner').style.display = 'none';
    document.getElementById('dice-prompt').textContent = 'Đang tung xúc xắc...';

    // Rolling animation — show random faces
    ['die-1','die-2','die-3'].forEach(id => document.getElementById(id).classList.add('rolling'));
    const rollInterval = setInterval(() => {
        ['die-1','die-2','die-3'].forEach(id => renderDieRolling(id));
    }, 80);

    try {
        const freshCsrf = document.querySelector('meta[name="csrf-token"]').content;
        const resp = await fetch('/api/dice', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': freshCsrf, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
            body: JSON.stringify({ bet_amount: amount, bet_type: selectedDiceType }),
        });
        const data = await resp.json();

        // Wait a bit so animation looks natural
        await new Promise(r => setTimeout(r, 800));

        clearInterval(rollInterval);
        ['die-1','die-2','die-3'].forEach(id => document.getElementById(id).classList.remove('rolling'));

        if (!data.success) {
            showToast(data.message, 'error');
        } else {
            // Show final dice with dot pattern
            renderDie('die-1', data.dice[0]);
            renderDie('die-2', data.dice[1]);
            renderDie('die-3', data.dice[2]);

            const totalEl = document.getElementById('dice-total');
            totalEl.textContent = data.total;
            totalEl.style.color = data.result === 'tai' ? '#ef4444' : '#60a5fa';

            // Show result banner
            const banner = document.getElementById('result-banner');
            if (data.is_triplet) {
                banner.style.background = 'rgba(245,158,11,0.15)';
                banner.style.border = '1px solid rgba(245,158,11,0.4)';
                banner.style.color = '#f59e0b';
                banner.textContent = '😱 BÃO! Banker thắng hết!';
            } else if (data.won) {
                banner.style.background = 'rgba(16,185,129,0.12)';
                banner.style.border = '1px solid rgba(16,185,129,0.4)';
                banner.style.color = '#10b981';
                banner.textContent = `🎉 THẮNG! +${(data.payout - amount).toFixed(0)} PT`;
            } else {
                banner.style.background = 'rgba(239,68,68,0.1)';
                banner.style.border = '1px solid rgba(239,68,68,0.3)';
                banner.style.color = '#ef4444';
                banner.textContent = `💸 THUA! -${amount} PT`;
            }
            banner.style.display = 'block';

            document.getElementById('dice-prompt').textContent = data.result_label;

            // Update balance
            userBalance = parseFloat(data.new_balance.replace(/,/g,''));
            document.getElementById('dice-balance').textContent = data.new_balance + ' PT';
            updateNavBalance(data.new_balance);

            // Sync with mobile header status bar
            const mNavBalance = document.getElementById('m-nav-balance');
            if (mNavBalance) mNavBalance.textContent = data.new_balance.split('.')[0];

            showToast(data.message, data.won ? 'success' : 'error');
        }
    } catch (e) {
        clearInterval(rollInterval);
        ['die-1','die-2','die-3'].forEach(id => document.getElementById(id).classList.remove('rolling'));
        showToast('Lỗi kết nối', 'error');
    }

    isRolling = false;
    btn.disabled = false;
    updateDiceBtn();
}
</script>
@endpush
