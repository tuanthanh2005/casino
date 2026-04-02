@extends('layouts.app')
@section('title', 'Kéo Búa Bao')

@push('styles')
<style>
.rps-layout { display:grid; grid-template-columns: 1fr 360px; gap:1.25rem; align-items:start; }
@media (max-width: 920px) { .rps-layout { grid-template-columns: 1fr; } }

.rps-arena {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 22px;
    padding: 1.25rem;
}

.rps-choice-grid { display:grid; grid-template-columns: repeat(3,1fr); gap:0.75rem; margin-top:0.75rem; }
.rps-choice-btn {
    border:1px solid var(--border);
    background: var(--bg-card2);
    border-radius: 14px;
    padding: 0.85rem 0.5rem;
    cursor:pointer;
    color: var(--text);
    font-weight: 700;
    font-family: 'Inter', sans-serif;
}
.rps-choice-btn.active { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(6,182,212,0.15); }

.mode-btns { display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; }
.mode-btn {
    border:1px solid var(--border);
    background: var(--bg-card2);
    border-radius: 10px;
    padding: 0.65rem;
    font-weight:700;
    color:var(--text-muted);
    cursor:pointer;
    font-family: 'Inter', sans-serif;
}
.mode-btn.active { background: rgba(6,182,212,0.12); color: var(--primary); border-color: var(--primary); }

.preset-btn { padding:0.38rem 0.75rem; border:1px solid var(--border); border-radius:9px; background:var(--bg-card2); color:var(--text-muted); cursor:pointer; font-size:0.8rem; }
.preset-btn:hover { border-color: var(--accent); color: var(--accent); }

.round-item { display:flex; justify-content:space-between; gap:0.5rem; padding:0.5rem 0.65rem; border-radius:10px; background:var(--bg-card2); margin-bottom:0.45rem; font-size:0.82rem; }
.tab-btn { border:none; background:transparent; color:var(--text-muted); padding:0.45rem 0.8rem; border-radius:8px; cursor:pointer; font-weight:700; }
.tab-btn.active { background: rgba(6,182,212,0.15); color:var(--primary); }
</style>
@endpush

@section('content')
<div class="page-enter">
    <div style="margin-bottom:1rem">
        <h1 style="font-size:1.65rem; font-weight:900">✊ Kéo Búa Bao</h1>
        <p style="margin-top:0.25rem; color:var(--text-muted)">1 click chơi nhanh · hỗ trợ mode Best-of-3</p>
    </div>

    <div class="rps-layout">
        <div class="rps-arena">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:0.75rem; flex-wrap:wrap">
                <div>
                    <div style="font-size:0.78rem; color:var(--text-muted)">Số dư</div>
                    <div id="rps-balance" style="font-size:1.25rem; font-weight:800; color:var(--accent)">{{ number_format((float)auth()->user()->balance_point, 2) }} PT</div>
                </div>
                <div class="mode-btns" style="min-width:230px">
                    <button class="mode-btn active" id="mode-single" onclick="setMode('single')">Single</button>
                    <button class="mode-btn" id="mode-bo3" onclick="setMode('bo3')">Best of 3</button>
                </div>
            </div>

            <div class="rps-choice-grid">
                <button class="rps-choice-btn" id="choice-keo" onclick="setChoice('keo')">✌️ Kéo</button>
                <button class="rps-choice-btn" id="choice-bua" onclick="setChoice('bua')">✊ Búa</button>
                <button class="rps-choice-btn" id="choice-bao" onclick="setChoice('bao')">🖐️ Bao</button>
            </div>

            <div style="margin-top:1rem">
                <label class="form-label">Số Point đặt cược</label>
                <div style="display:flex; gap:0.45rem; flex-wrap:wrap; margin-bottom:0.65rem">
                    @foreach([10,50,100,500,1000] as $p)
                    <button class="preset-btn" onclick="setAmount({{ $p }})">{{ number_format($p) }}</button>
                    @endforeach
                    <button class="preset-btn" onclick="setAmount(Math.floor({{ (float)auth()->user()->balance_point }}))">MAX</button>
                </div>
                <input id="rps-amount" type="number" class="form-control" min="1" placeholder="Nhập số điểm..." oninput="updatePreview()">
            </div>

            <div style="margin-top:0.75rem; background:var(--bg-card2); border-radius:10px; padding:0.7rem">
                <div style="font-size:0.8rem; color:var(--text-muted)">Dự kiến nếu thắng:</div>
                <div id="rps-preview" style="font-weight:800; color:var(--accent)">0 PT</div>
            </div>

            <button id="rps-play-btn" class="btn btn-primary w-100" style="margin-top:0.9rem; height:46px; font-weight:800" onclick="playRps()">🎮 Chơi ngay</button>

            <div style="margin-top:1rem">
                <div style="font-weight:700; margin-bottom:0.55rem">Kết quả ván gần nhất</div>
                <div id="rps-final" style="font-size:0.9rem; color:var(--text-muted); margin-bottom:0.5rem">Chưa có dữ liệu.</div>
                <div id="rps-rounds"></div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:1rem">
            <div class="card">
                <div class="card-header" style="justify-content:space-between">
                    <span>🏆 Leaderboard</span>
                    <div>
                        <button class="tab-btn active" id="lb-day-btn" onclick="switchBoard('day')">Ngày</button>
                        <button class="tab-btn" id="lb-week-btn" onclick="switchBoard('week')">Tuần</button>
                    </div>
                </div>
                <div class="card-body" style="padding:0.75rem">
                    <div id="lb-day">
                        @forelse($leaderboardToday as $i => $p)
                        <div class="round-item">
                            <span>#{{ $i + 1 }} · {{ $p->user->name ?? 'N/A' }} ({{ $p->win_rate }}%)</span>
                            <span style="font-weight:800; color:{{ $p->total_profit >= 0 ? '#10b981' : '#ef4444' }}">{{ $p->total_profit >= 0 ? '+' : '' }}{{ number_format((float)$p->total_profit, 0) }} PT</span>
                        </div>
                        @empty
                        <div style="color:var(--text-muted); text-align:center; padding:1rem">Chưa có dữ liệu hôm nay</div>
                        @endforelse
                    </div>
                    <div id="lb-week" style="display:none">
                        @forelse($leaderboardWeek as $i => $p)
                        <div class="round-item">
                            <span>#{{ $i + 1 }} · {{ $p->user->name ?? 'N/A' }} ({{ $p->win_rate }}%)</span>
                            <span style="font-weight:800; color:{{ $p->total_profit >= 0 ? '#10b981' : '#ef4444' }}">{{ $p->total_profit >= 0 ? '+' : '' }}{{ number_format((float)$p->total_profit, 0) }} PT</span>
                        </div>
                        @empty
                        <div style="color:var(--text-muted); text-align:center; padding:1rem">Chưa có dữ liệu tuần này</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><span>🕘 Lịch sử của bạn</span></div>
                <div class="card-body" style="padding:0.5rem 0.75rem; max-height:360px; overflow:auto">
                    @forelse($history as $h)
                    @php
                        $d = $h->details ?? [];
                        $m = $d['mode'] ?? 'single';
                        $f = $d['final'] ?? '';
                    @endphp
                    <div class="round-item">
                        <span>{{ strtoupper($m) }} · {{ $h->created_at->format('d/m H:i') }} · {{ $f }}</span>
                        <span style="font-weight:800; color:{{ $h->profit >= 0 ? '#10b981' : '#ef4444' }}">{{ $h->profit >= 0 ? '+' : '' }}{{ number_format((float)$h->profit, 0) }}</span>
                    </div>
                    @empty
                    <div style="color:var(--text-muted); text-align:center; padding:1rem">Chưa có lịch sử</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let RPS_MODE = 'single';
let RPS_CHOICE = 'keo';

function setMode(mode) {
    RPS_MODE = mode;
    document.getElementById('mode-single').classList.toggle('active', mode === 'single');
    document.getElementById('mode-bo3').classList.toggle('active', mode === 'bo3');
    updatePreview();
}

function setChoice(choice) {
    RPS_CHOICE = choice;
    ['keo', 'bua', 'bao'].forEach(c => {
        document.getElementById('choice-' + c).classList.toggle('active', c === choice);
    });
}

function setAmount(v) {
    document.getElementById('rps-amount').value = v;
    updatePreview();
}

function updatePreview() {
    const amount = parseFloat(document.getElementById('rps-amount').value || '0');
    const mult = RPS_MODE === 'bo3' ? 2.7 : 1.95;
    const win = Math.floor(amount * mult);
    document.getElementById('rps-preview').textContent = isNaN(win) ? '0 PT' : win.toLocaleString('vi-VN') + ' PT';
}

function switchBoard(type) {
    document.getElementById('lb-day').style.display = type === 'day' ? '' : 'none';
    document.getElementById('lb-week').style.display = type === 'week' ? '' : 'none';
    document.getElementById('lb-day-btn').classList.toggle('active', type === 'day');
    document.getElementById('lb-week-btn').classList.toggle('active', type === 'week');
}

function labelChoice(c) {
    if (c === 'keo') return '✌️ Kéo';
    if (c === 'bua') return '✊ Búa';
    return '🖐️ Bao';
}

async function playRps() {
    const amount = parseFloat(document.getElementById('rps-amount').value || '0');
    if (!amount || amount <= 0) {
        showToast('Nhập số điểm hợp lệ', 'error');
        return;
    }

    const btn = document.getElementById('rps-play-btn');
    btn.disabled = true;
    btn.textContent = 'Đang xử lý...';

    try {
        const resp = await fetch('/api/rps', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ bet_amount: amount, choice: RPS_CHOICE, mode: RPS_MODE }),
        });
        const data = await resp.json();

        if (!data.success) {
            showToast(data.message || 'Không thể chơi lúc này', 'error');
            return;
        }

        document.getElementById('rps-balance').textContent = data.new_balance + ' PT';
        document.getElementById('rps-final').innerHTML =
            `<strong>${data.final.toUpperCase()}</strong> · Score ${data.score.user}-${data.score.bot} (hòa ${data.score.draw}) · Profit <strong style="color:${data.profit >= 0 ? '#10b981' : '#ef4444'}">${data.profit >= 0 ? '+' : ''}${Number(data.profit).toLocaleString('vi-VN')} PT</strong>`;

        const rounds = document.getElementById('rps-rounds');
        rounds.innerHTML = '';
        (data.rounds || []).forEach(r => {
            const div = document.createElement('div');
            div.className = 'round-item';
            div.innerHTML = `<span>Round ${r.round}: ${labelChoice(r.player)} vs ${labelChoice(r.bot)}</span><span style="font-weight:800">${r.result.toUpperCase()}</span>`;
            rounds.appendChild(div);
        });

        showToast(data.message || 'Đã xử lý xong', data.won ? 'success' : 'error');
    } catch (e) {
        showToast('Lỗi kết nối máy chủ', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = '🎮 Chơi ngay';
    }
}

setChoice('keo');
updatePreview();
</script>
@endpush
