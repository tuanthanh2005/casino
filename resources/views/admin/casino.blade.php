@extends('layouts.admin')
@section('title', 'Casino Stats & Cấu hình')

@push('admin-styles')
<style>
.stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
@media(max-width:900px){ .stat-grid{grid-template-columns:repeat(2,1fr)} }
.pct-bar { height:6px; border-radius:4px; background:rgba(255,255,255,0.08); overflow:hidden; margin-top:0.5rem; }
.pct-fill { height:100%; border-radius:4px; transition:width 1.2s ease; }
.game-tab-btns { display:flex; gap:0.75rem; margin-bottom:1.5rem; }
.game-tab { padding:0.5rem 1.25rem; border-radius:8px; border:1px solid var(--border); background:transparent; color:var(--text-muted); cursor:pointer; font-family:'Inter',sans-serif; font-weight:600; font-size:0.8rem; transition:all 0.2s; }
.game-tab.active { background:var(--primary); border-color:var(--primary); color:white; }
.cfg-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
@media(max-width:700px){ .cfg-grid{grid-template-columns:1fr} }
.toggle-wrap { display:flex; align-items:center; gap:0.75rem; }
.toggle { position:relative; width:44px; height:24px; }
.toggle input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; inset:0; background:var(--bg-card2); border:1px solid var(--border); border-radius:24px; cursor:pointer; transition:0.3s; }
.toggle-slider:before { content:''; position:absolute; width:18px; height:18px; top:2px; left:2px; background:var(--text-muted); border-radius:50%; transition:0.3s; }
.toggle input:checked + .toggle-slider { background:var(--primary); border-color:var(--primary); }
.toggle input:checked + .toggle-slider:before { transform:translateX(20px); background:white; }
.range-wrap { position:relative; }
.range-input { width:100%; accent-color:var(--primary); }
.range-label { display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem; }
</style>
@endpush

@section('admin-content')
<div class="page-header" style="display:flex; align-items:center; justify-content:space-between">
    <div>
        <h1>🎰 Casino Stats</h1>
        <p>Thống kê & cấu hình tỷ lệ thắng/thua · Vòng Quay và Tài Xỉu</p>
    </div>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- CẤU HÌNH CASINO --}}
{{-- ═══════════════════════════════════════ --}}
<div class="card" style="margin-bottom:1.5rem; border-color:rgba(245,158,11,0.3)">
    <div class="card-header" style="background:rgba(245,158,11,0.05)">
        <span>⚙️ Cấu hình Casino — Admin</span>
        <button id="save-settings-btn" onclick="saveCasinoSettings()" class="btn btn-warning" style="font-size:0.8rem">
            <i class="bi bi-floppy"></i> Lưu thay đổi
        </button>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem">

            {{-- VÒNG QUAY --}}
            <div>
                <div style="font-weight:700; font-size:0.95rem; margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--border)">
                    🎡 Vòng Quay May Mắn
                </div>
                <div style="display:flex; flex-direction:column; gap:1rem">
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="checkbox" id="cfg_spin_enabled" {{ $settings->get('spin_enabled','1') == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.875rem">Bật Vòng Quay</span>
                    </div>
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="checkbox" id="cfg_spin_house_edge" {{ $settings->get('spin_house_edge','1') == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.875rem">Bật House Edge (anti win-streak)</span>
                    </div>

                    <div>
                        <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">
                            Tỷ lệ thắng mục tiêu: <strong id="spin_target_val" style="color:var(--accent)">{{ $settings->get('spin_win_rate_target','40') }}%</strong>
                        </label>
                        <input type="range" class="range-input" id="cfg_spin_win_rate_target"
                               min="5" max="70" step="1" value="{{ $settings->get('spin_win_rate_target','40') }}"
                               oninput="document.getElementById('spin_target_val').textContent=this.value+'%'">
                        <div class="range-label"><span>5% (casino)</span><span>35% (fair)</span><span>70% (generous)</span></div>
                    </div>

                    <div>
                        <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">
                            Win streak limit (kích hoạt house edge): <strong id="spin_limit_val" style="color:#ef4444">{{ $settings->get('spin_win_rate_limit','60') }}%</strong>
                        </label>
                        <input type="range" class="range-input" id="cfg_spin_win_rate_limit"
                               min="30" max="95" step="5" value="{{ $settings->get('spin_win_rate_limit','60') }}"
                               oninput="document.getElementById('spin_limit_val').textContent=this.value+'%'">
                        <div class="range-label"><span>30%</span><span>60%</span><span>95%</span></div>
                    </div>

                    <div>
                        <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Cược tối đa (PT, 0 = không giới hạn)</label>
                        <input type="number" class="form-control" id="cfg_spin_max_bet" value="{{ $settings->get('spin_max_bet','10000') }}" min="0" step="100">
                    </div>
                </div>
            </div>

            {{-- TÀI XỈU --}}
            <div>
                <div style="font-weight:700; font-size:0.95rem; margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--border)">
                    🎲 Tài Xỉu
                </div>
                <div style="display:flex; flex-direction:column; gap:1rem">
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="checkbox" id="cfg_dice_enabled" {{ $settings->get('dice_enabled','1') == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.875rem">Bật Tài Xỉu</span>
                    </div>
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="checkbox" id="cfg_dice_house_edge" {{ $settings->get('dice_house_edge','1') == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:0.875rem">Bật House Edge (anti win-streak)</span>
                    </div>

                    <div>
                        <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">
                            Win streak limit (kích hoạt house edge): <strong id="dice_limit_val" style="color:#ef4444">{{ $settings->get('dice_win_rate_limit','65') }}%</strong>
                        </label>
                        <input type="range" class="range-input" id="cfg_dice_win_rate_limit"
                               min="30" max="95" step="5" value="{{ $settings->get('dice_win_rate_limit','65') }}"
                               oninput="document.getElementById('dice_limit_val').textContent=this.value+'%'">
                        <div class="range-label"><span>30%</span><span>65%</span><span>95%</span></div>
                    </div>

                    <div>
                        <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">
                            Hệ số trả thưởng (x<strong id="dice_mult_val">{{ $settings->get('dice_payout_mult','1.95') }}</strong>)
                        </label>
                        <input type="range" class="range-input" id="cfg_dice_payout_mult"
                               min="1.0" max="2.5" step="0.05" value="{{ $settings->get('dice_payout_mult','1.95') }}"
                               oninput="document.getElementById('dice_mult_val').textContent=parseFloat(this.value).toFixed(2)">
                        <div class="range-label"><span>x1.0</span><span>x1.95</span><span>x2.5</span></div>
                    </div>

                    <div>
                        <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Cược tối đa (PT, 0 = không giới hạn)</label>
                        <input type="number" class="form-control" id="cfg_dice_max_bet" value="{{ $settings->get('dice_max_bet','10000') }}" min="0" step="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- OVERALL STATS --}}
{{-- ═══════════════════════════════════════ --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(99,102,241,0.15)"><i class="bi bi-dice-5" style="color:#818cf8"></i></div>
        <div class="stat-value" style="color:var(--primary)">{{ $totalGames }}</div>
        <div class="stat-label">Tổng ván (24h)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.15)"><i class="bi bi-percent" style="color:#ef4444"></i></div>
        <div class="stat-value" style="color:#ef4444">{{ $overallWinRate }}%</div>
        <div class="stat-label">Tỷ lệ thắng chung (24h)</div>
        <div class="pct-bar"><div class="pct-fill" style="width:{{$overallWinRate}}%; background:#ef4444"></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.15)"><i class="bi bi-cash-coin" style="color:#10b981"></i></div>
        <div class="stat-value" style="color:#10b981">+{{ number_format((float)$houseProfit, 0) }}</div>
        <div class="stat-label">House profit (24h) PT</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15)"><i class="bi bi-person-check" style="color:#f59e0b"></i></div>
        <div class="stat-value" style="color:#f59e0b">{{ $winnersCount }}</div>
        <div class="stat-label">Users đang có lãi (24h)</div>
    </div>
</div>

{{-- GAME TABS --}}
<div class="game-tab-btns">
    <button class="game-tab active" id="tab-spin-btn" onclick="showTab('spin')">🎡 Vòng Quay</button>
    <button class="game-tab" id="tab-dice-btn" onclick="showTab('dice')">🎲 Tài Xỉu</button>
</div>

{{-- SPIN STATS --}}
<div id="tab-spin">
    <div class="stat-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card">
            <div class="stat-label">Tổng ván Spin (24h)</div>
            <div class="stat-value" style="color:var(--accent); font-size:1.4rem">{{ $spinStats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Win rate thực tế</div>
            <div class="stat-value" style="color:#60a5fa; font-size:1.4rem">{{ $spinStats['win_rate'] }}%</div>
            <div class="pct-bar"><div class="pct-fill" style="width:{{$spinStats['win_rate']}}%; background:#6366f1"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">House profit Spin</div>
            <div class="stat-value" style="color:#10b981; font-size:1.4rem">+{{ number_format((float)$spinStats['house_profit'], 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tổng điểm đặt cược</div>
            <div class="stat-value" style="font-size:1.4rem">{{ number_format((float)$spinStats['total_bet'], 0) }}</div>
        </div>
    </div>

    <div class="card" style="margin-top:1rem">
        <div class="card-header"><span><i class="bi bi-trophy"></i> Top người thắng — Vòng Quay (24h)</span></div>
        <div style="max-height:340px; overflow-y:auto; scrollbar-width:thin">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Người chơi</th><th>Ván</th><th>Win Rate</th><th>Profit</th><th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spinTopPlayers as $i => $p)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td style="font-weight:600">{{ $p->user->name ?? 'N/A' }}</td>
                        <td>{{ $p->total_games }}</td>
                        <td><span style="color:{{ $p->win_rate > 60 ? '#ef4444' : '#10b981' }}; font-weight:700">{{ $p->win_rate }}%</span></td>
                        <td>
                            @if($p->total_profit > 0)
                                <span style="color:#10b981">+{{ number_format($p->total_profit, 0) }} PT</span>
                            @else
                                <span style="color:#ef4444">{{ number_format($p->total_profit, 0) }} PT</span>
                            @endif
                        </td>
                        <td>
                            @if($p->win_rate > 65)
                                <span class="badge badge-danger">⚠️ Win streak cao</span>
                            @elseif($p->win_rate > 50)
                                <span class="badge badge-warning">Đang thắng</span>
                            @else
                                <span class="badge badge-success">Bình thường</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center" style="color:var(--text-muted); padding:2rem">Chưa có dữ liệu trong 24 giờ qua</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- DICE STATS --}}
<div id="tab-dice" style="display:none">
    <div class="stat-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card">
            <div class="stat-label">Tổng ván Tài Xỉu (24h)</div>
            <div class="stat-value" style="color:var(--accent); font-size:1.4rem">{{ $diceStats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Win rate thực tế</div>
            <div class="stat-value" style="color:#60a5fa; font-size:1.4rem">{{ $diceStats['win_rate'] }}%</div>
            <div class="pct-bar"><div class="pct-fill" style="width:{{$diceStats['win_rate']}}%; background:#3b82f6"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">House profit Tài Xỉu</div>
            <div class="stat-value" style="color:#10b981; font-size:1.4rem">+{{ number_format((float)$diceStats['house_profit'], 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Bão (Triplet)</div>
            <div class="stat-value" style="color:#f59e0b; font-size:1.4rem">{{ $diceStats['triplets'] }}</div>
            <div class="stat-label">→ Banker thắng tất</div>
        </div>
    </div>

    <div class="card" style="margin-top:1rem">
        <div class="card-header"><span><i class="bi bi-trophy"></i> Top người thắng — Tài Xỉu (24h)</span></div>
        <div style="max-height:340px; overflow-y:auto; scrollbar-width:thin">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Người chơi</th><th>Ván</th><th>Win Rate</th><th>Profit</th><th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diceTopPlayers as $i => $p)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td style="font-weight:600">{{ $p->user->name ?? 'N/A' }}</td>
                        <td>{{ $p->total_games }}</td>
                        <td><span style="color:{{ $p->win_rate > 65 ? '#ef4444' : '#10b981' }}; font-weight:700">{{ $p->win_rate }}%</span></td>
                        <td>
                            @if($p->total_profit > 0)
                                <span style="color:#10b981">+{{ number_format($p->total_profit, 0) }} PT</span>
                            @else
                                <span style="color:#ef4444">{{ number_format($p->total_profit, 0) }} PT</span>
                            @endif
                        </td>
                        <td>
                            @if($p->win_rate > 65)
                                <span class="badge badge-danger">⚠️ Win streak cao</span>
                            @elseif($p->win_rate > 50)
                                <span class="badge badge-warning">Đang thắng</span>
                            @else
                                <span class="badge badge-success">Bình thường</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center" style="color:var(--text-muted); padding:2rem">Chưa có dữ liệu trong 24 giờ qua</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function showTab(tab) {
    ['spin','dice'].forEach(t => {
        document.getElementById(`tab-${t}`).style.display = t === tab ? '' : 'none';
        document.getElementById(`tab-${t}-btn`).classList.toggle('active', t === tab);
    });
}

async function saveCasinoSettings() {
    const btn = document.getElementById('save-settings-btn');
    btn.disabled = true;
    btn.textContent = '⏳ Đang lưu...';

    const payload = {
        spin_enabled:         document.getElementById('cfg_spin_enabled').checked ? '1' : '0',
        spin_house_edge:      document.getElementById('cfg_spin_house_edge').checked ? '1' : '0',
        spin_win_rate_limit:  document.getElementById('cfg_spin_win_rate_limit').value,
        spin_win_rate_target: document.getElementById('cfg_spin_win_rate_target').value,
        spin_max_bet:         document.getElementById('cfg_spin_max_bet').value,
        dice_enabled:         document.getElementById('cfg_dice_enabled').checked ? '1' : '0',
        dice_house_edge:      document.getElementById('cfg_dice_house_edge').checked ? '1' : '0',
        dice_win_rate_limit:  document.getElementById('cfg_dice_win_rate_limit').value,
        dice_payout_mult:     document.getElementById('cfg_dice_payout_mult').value,
        dice_max_bet:         document.getElementById('cfg_dice_max_bet').value,
    };

    try {
        const resp = await fetch('{{ route("admin.casino.settings") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await resp.json();
        showToast(data.message, data.success ? 'success' : 'error');
    } catch(e) {
        showToast('Lỗi kết nối', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-floppy"></i> Lưu thay đổi';
}
</script>
@endpush
