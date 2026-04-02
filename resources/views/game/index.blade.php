@extends('layouts.app')

@section('title', 'Game Dự Đoán BTC')

@push('styles')
<style>
    /* BTC PRICE DISPLAY */
    .price-section {
        background: linear-gradient(135deg, #0d1117 0%, #111827 50%, #0d1117 100%);
        border: 1px solid rgba(99,102,241,0.3);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .price-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 50% 50%, rgba(99,102,241,0.06) 0%, transparent 60%);
        animation: pulse-bg 4s ease-in-out infinite;
    }

    @keyframes pulse-bg {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.05); }
    }

    .btc-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        font-weight: 500;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        animation: blink 1s ease-in-out infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; box-shadow: 0 0 6px #10b981; }
        50% { opacity: 0.4; box-shadow: none; }
    }

    .btc-price {
        font-size: 3.5rem;
        font-weight: 900;
        letter-spacing: -2px;
        background: linear-gradient(135deg, #fff, #a5b4fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1;
        transition: color 0.3s;
        position: relative;
    }

    .btc-price.up {
        background: linear-gradient(135deg, #10b981, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btc-price.down {
        background: linear-gradient(135deg, #ef4444, #f87171);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .price-change {
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 0.5rem;
        transition: all 0.3s;
    }

    .price-meta {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .price-meta-item {
        text-align: center;
    }

    .price-meta-item .label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .price-meta-item .value {
        font-size: 1rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    /* SESSION TIMER */
    .session-section {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .session-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .session-id {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .session-timer {
        font-size: 2rem;
        font-weight: 900;
        color: var(--accent);
        font-variant-numeric: tabular-nums;
    }

    .session-timer.urgent { color: var(--danger); animation: pulse-red 0.5s ease-in-out infinite; }

    @keyframes pulse-red {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    .progress-bar-wrap {
        background: var(--bg-card2);
        border-radius: 100px;
        height: 6px;
        overflow: hidden;
        margin-top: 0.75rem;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--accent));
        border-radius: 100px;
        transition: width 1s linear;
    }

    .session-price-info {
        display: flex;
        justify-content: space-between;
        margin-top: 0.75rem;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* BET FORM */
    .bet-section {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .bet-section-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .bet-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .btn-long-big, .btn-short-big {
        padding: 1.25rem;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.3rem;
        font-family: 'Inter', sans-serif;
    }

    .btn-long-big {
        background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
        border-color: rgba(16,185,129,0.4);
        color: #10b981;
    }

    .btn-long-big:hover, .btn-long-big.active {
        background: linear-gradient(135deg, #059669, #10b981);
        border-color: #10b981;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(16,185,129,0.4);
    }

    .btn-short-big {
        background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05));
        border-color: rgba(239,68,68,0.4);
        color: #ef4444;
    }

    .btn-short-big:hover, .btn-short-big.active {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        border-color: #ef4444;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(239,68,68,0.4);
    }

    .bet-type-icon { font-size: 1.8rem; }
    .bet-type-sub { font-size: 0.75rem; font-weight: 500; opacity: 0.8; }

    .amount-presets {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
    }

    .amount-preset {
        padding: 0.35rem 0.75rem;
        background: var(--bg-card2);
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.8rem;
        cursor: pointer;
        color: var(--text-muted);
        transition: all 0.15s;
    }

    .amount-preset:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .profit-preview {
        background: var(--bg-card2);
        border-radius: 10px;
        padding: 0.875rem 1rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.875rem;
    }

    .profit-preview .label { color: var(--text-muted); }
    .profit-preview .value { font-weight: 700; color: var(--accent); }

    .btn-place-bet {
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 800;
        font-family: 'Inter', sans-serif;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-place-bet:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99,102,241,0.4);
    }

    .btn-place-bet:disabled {
        background: var(--bg-card2);
        color: var(--text-muted);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* BET HISTORY */
    .bet-row-won { border-left: 3px solid var(--success); }
    .bet-row-lost { border-left: 3px solid var(--danger); }
    .bet-row-pending { border-left: 3px solid var(--accent); }

    .text-long { color: #10b981; font-weight: 700; }
    .text-short { color: #ef4444; font-weight: 700; }

    /* LAYOUT */
    .game-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1.5rem;
        align-items: start;
    }

    /* ── MOBILE APP STYLE ── */
    @media (max-width: 768px) {
        .game-layout {
            display: block !important;
            padding: 0 !important;
        }
        
        .price-section {
            padding: 1.5rem 1rem !important;
            margin-bottom: 1rem !important;
            border-radius: 16px !important;
        }
        .btc-price {
            font-size: 2.2rem !important; /* Fixed overflow */
            letter-spacing: -1px !important;
        }
        .price-meta {
            gap: 1rem !important;
            margin-top: 1rem !important;
        }
        .price-meta-item .value { font-size: 0.9rem !important; }

        .session-section {
            padding: 1rem !important;
            margin-bottom: 1rem !important;
        }
        .session-timer { font-size: 1.5rem !important; }

        .bet-section {
            padding: 1rem !important;
            position: relative !important;
        }
        .btn-long-big, .btn-short-big {
            padding: 1rem 0.5rem !important;
        }
        .bet-type-icon { font-size: 1.4rem !important; }
        .bet-type-sub { display: none; } /* Hide sub-text to keep it clean */
        
        .amount-presets { justify-content: center; }
        
        .card-header { display: none !important; } /* Replaced by mobile conventions */
        
        .desktop-only { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="page-enter">
    <div class="game-layout">
        <!-- LEFT: MAIN GAME -->
    <div>
        <!-- BTC Price Display -->
        <div class="price-section">
            <div class="btc-label">
                <span class="live-dot"></span>
                BTC / USDT · LIVE
            </div>
            <div class="btc-price" id="btc-price">$---.---</div>
            <div class="price-change" id="price-change">
                <span id="change-icon">↔</span>
                <span id="change-value">+$0.00</span>
            </div>
            <div class="price-meta">
                <div class="price-meta-item">
                    <div class="label">Mở phiên</div>
                    <div class="value" id="session-start-price">
                        ${{ $activeSession ? number_format($activeSession->start_price, 2) : '---' }}
                    </div>
                </div>
                <div class="price-meta-item">
                    <div class="label">Phiên #</div>
                    <div class="value" id="session-number">{{ $activeSession ? $activeSession->id : '---' }}</div>
                </div>
                <div class="price-meta-item">
                    <div class="label">Tổng cược</div>
                    <div class="value" id="total-bets">{{ $activeSession ? $activeSession->bets->count() : 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Session Timer -->
        <div class="session-section">
            <div class="session-header">
                <div>
                    <div style="font-weight:700">⏱ Đếm ngược phiên</div>
                    <div class="session-id">Phiên #<span id="sid">{{ $activeSession?->id ?? '?' }}</span></div>
                </div>
                <div class="session-timer" id="countdown">00:60</div>
            </div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" id="progress-bar" style="width:100%"></div>
            </div>
            <div class="session-price-info">
                <span>Giá mở: ${{ $activeSession ? number_format($activeSession->start_price, 2) : '---' }}</span>
                <span id="session-end-text">Kết thúc: lúc <span id="session-end-time">{{ $activeSession ? $activeSession->end_time->format('H:i:s') : '--:--' }}</span></span>
            </div>
        </div>

        <!-- Lịch sử cược -->
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-clock-history"></i> Lịch sử cược của bạn</span>
                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:400">10 gần nhất</span>
            </div>
            {{-- max-height ~10 rows, sticky thead --}}
            <div style="max-height:420px; overflow-y:auto; scrollbar-width:thin; scrollbar-color:rgba(99,102,241,0.4) transparent;">
                <table style="width:100%; border-collapse:collapse; font-size:0.875rem">
                    <thead>
                        <tr style="position:sticky; top:0; background:var(--bg-card2); z-index:2;">
                            <th style="padding:0.6rem 1rem; text-align:left; color:var(--text-muted); font-size:0.75rem; font-weight:600; border-bottom:1px solid var(--border)">#</th>
                            <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-size:0.75rem; font-weight:600; border-bottom:1px solid var(--border)">Phiên</th>
                            <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-size:0.75rem; font-weight:600; border-bottom:1px solid var(--border)">Cửa</th>
                            <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-size:0.75rem; font-weight:600; border-bottom:1px solid var(--border)">Số điểm</th>
                            <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-size:0.75rem; font-weight:600; border-bottom:1px solid var(--border)">Kết quả</th>
                            <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-size:0.75rem; font-weight:600; border-bottom:1px solid var(--border)">Lợi nhuận</th>
                            <th style="padding:0.6rem 0.75rem; text-align:left; color:var(--text-muted); font-size:0.75rem; font-weight:600; border-bottom:1px solid var(--border)">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody id="bets-table">
                        @forelse($myBets as $bet)
                        <tr class="bet-row-{{ $bet->status }}" style="border-bottom:1px solid rgba(255,255,255,0.04)">
                            <td style="padding:0.65rem 1rem">{{ $bet->id }}</td>
                            <td style="padding:0.65rem 0.75rem">#{{ $bet->session_id }}</td>
                            <td style="padding:0.65rem 0.75rem" class="text-{{ $bet->bet_type }}">
                                {{ $bet->bet_type === 'long' ? '▲ LONG' : '▼ SHORT' }}
                            </td>
                            <td style="padding:0.65rem 0.75rem">{{ number_format($bet->bet_amount, 0) }} PT</td>
                            <td style="padding:0.65rem 0.75rem">{!! $bet->status_label !!}</td>
                            <td style="padding:0.65rem 0.75rem">
                                @if($bet->status === 'won')
                                    <span style="color:#10b981">+{{ number_format($bet->profit, 2) }} PT</span>
                                @elseif($bet->status === 'lost')
                                    <span style="color:#ef4444">-{{ number_format($bet->bet_amount, 0) }} PT</span>
                                @else
                                    <span style="color:#9ca3af">Chờ...</span>
                                @endif
                            </td>
                            <td style="padding:0.65rem 0.75rem; color:var(--text-muted)">{{ $bet->created_at->format('d/m H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:var(--text-muted); padding:2.5rem 1rem">
                                <div style="font-size:1.5rem; margin-bottom:0.5rem">📋</div>
                                Chưa có lịch sử cược. Hãy đặt cược ngay!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- RIGHT: BET PANEL -->
    <div>
        <!-- Active Bet Banner -->
        <div class="bet-active-banner" id="active-bet-banner" style="display:none">
            <div>
                <div style="font-size:0.8rem; color:var(--text-muted)">Bạn đã đặt phiên này</div>
                <div style="font-weight:700; margin-top:0.2rem" id="active-bet-info"></div>
            </div>
            <i class="bi bi-check2-circle" style="color:#10b981; font-size:1.5rem"></i>
        </div>

        <!-- Bet Form -->
        <div class="bet-section" id="bet-form-section">
            <div class="bet-section-title">
                <i class="bi bi-lightning-charge-fill" style="color:var(--accent)"></i>
                Đặt cược phiên #<span id="form-session-id">{{ $activeSession?->id ?? '?' }}</span>
            </div>

            <!-- Long/Short Selection -->
            <div class="bet-buttons">
                <button class="btn-long-big" id="btn-long" onclick="selectBetType('long')">
                    <span class="bet-type-icon">▲</span>
                    <span>LONG</span>
                    <span class="bet-type-sub">Giá sẽ tăng</span>
                </button>
                <button class="btn-short-big" id="btn-short" onclick="selectBetType('short')">
                    <span class="bet-type-icon">▼</span>
                    <span>SHORT</span>
                    <span class="bet-type-sub">Giá sẽ giảm</span>
                </button>
            </div>

            <!-- Amount -->
            <label class="form-label">Số Point đặt cược</label>
            <div class="amount-presets">
                <span class="amount-preset" onclick="setAmount(10)">10</span>
                <span class="amount-preset" onclick="setAmount(50)">50</span>
                <span class="amount-preset" onclick="setAmount(100)">100</span>
                <span class="amount-preset" onclick="setAmount(500)">500</span>
                <span class="amount-preset" onclick="setAmount(1000)">1K</span>
                <span class="amount-preset" onclick="setAmountMax()">MAX</span>
            </div>
            <input type="number" id="bet-amount" class="form-control mb-3"
                   placeholder="Nhập số Point..."
                   min="1" max="{{ auth()->user()->balance_point }}"
                   oninput="updateProfitPreview()">

            <!-- Profit Preview -->
            <div class="profit-preview">
                <span class="label">🏆 Thắng nhận về:</span>
                <span class="value" id="profit-preview">x1.95 = 0 PT</span>
            </div>

            <!-- Submit -->
            <button class="btn-place-bet" id="submit-bet" onclick="placeBet()" disabled>
                Chọn LONG hoặc SHORT để đặt cược
            </button>

            <div style="margin-top:0.75rem; font-size:0.75rem; color:var(--text-muted); text-align:center">
                Số dư: <strong style="color:var(--accent)" id="balance-display">{{ number_format(auth()->user()->balance_point, 2) }}</strong> PT
                &nbsp;|&nbsp; Phí sàn 5% (x1.95)
            </div>
        </div>

        <!-- Completed Sessions -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-data"></i> Kết quả gần đây
                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:400">(5 phiên gần nhất)</span>
            </div>
            <div style="padding:0">
                @forelse($completedSessions as $cs)
                <div style="padding:0.65rem 1.25rem; border-bottom:1px solid rgba(255,255,255,0.04); display:flex; align-items:center; justify-content:space-between; gap:0.5rem">
                    <div style="min-width:0">
                        <div style="font-size:0.75rem; color:var(--text-muted)">Phiên #{{ $cs->id }}</div>
                        <div style="font-size:0.8rem; margin-top:0.15rem; white-space:nowrap">
                            ${{ number_format((float)$cs->start_price, 0) }}
                            <span style="color:var(--text-muted); margin:0 2px">→</span>
                            ${{ number_format((float)$cs->end_price, 0) }}
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0">
                        @if($cs->end_price > $cs->start_price)
                            <div class="text-long" style="font-size:0.95rem; font-weight:800">▲ LONG</div>
                        @else
                            <div class="text-short" style="font-size:0.95rem; font-weight:800">▼ SHORT</div>
                        @endif
                        <div style="font-size:0.7rem; color:var(--text-muted)">{{ $cs->bets_count }} cược</div>
                    </div>
                </div>
                @empty
                <div class="text-center" style="padding:2rem; color:var(--text-muted)">
                    <div style="font-size:1.5rem; margin-bottom:0.5rem">🎮</div>
                    Chưa có phiên nào kết thúc
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ===== RESULT MODAL WIN/LOSE ===== --}}
<div id="result-modal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    <div id="result-modal-bg" style="position:absolute;inset:0;background:rgba(0,0,0,0.85);backdrop-filter:blur(8px);" onclick="closeResultModal()"></div>
    <div id="result-modal-box" style="
        background: var(--bg-card);
        border-radius: 24px;
        padding: 2.5rem 2rem;
        max-width: 400px;
        width: 100%;
        text-align: center;
        position: relative;
        transform: scale(0.5);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 1;
    ">
        <canvas id="confetti-canvas" style="position:absolute;inset:0;pointer-events:none;border-radius:24px;width:100%;height:100%"></canvas>

        <div id="result-icon" style="font-size:4rem; margin-bottom:1rem; display:inline-block"></div>
        <div id="result-title" style="font-size:1.8rem; font-weight:900; margin-bottom:0.5rem"></div>
        <div id="result-subtitle" style="color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem"></div>

        <div id="result-profit-box" style="border-radius: 14px; padding: 1rem 1.5rem; margin-bottom: 1.5rem;">
            <div style="font-size:0.8rem; margin-bottom:0.25rem; opacity:0.8" id="result-profit-label">Nhận về</div>
            <div id="result-profit-amount" style="font-size:2rem; font-weight:900; font-variant-numeric:tabular-nums"></div>
        </div>

        <div style="background:rgba(255,255,255,0.05); border-radius:10px; padding:0.75rem 1rem; font-size:0.8rem; color:var(--text-muted); margin-bottom:1.5rem; display:flex; justify-content:space-between;">
            <span>Giá mở: <strong id="result-start-price" style="color:var(--text)"></strong></span>
            <span id="result-direction-arrow"></span>
            <span>Giá đóng: <strong id="result-end-price" style="color:var(--text)"></strong></span>
        </div>

        <button onclick="closeResultModal()" id="result-close-btn" style="
            width:100%; padding:0.875rem; border:none; border-radius:12px;
            font-size:1rem; font-weight:700; cursor:pointer; font-family:'Inter',sans-serif;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; transition:all 0.2s;
        ">Tiếp tục chơi 🚀</button>
        <div style="margin-top:0.75rem; font-size:0.75rem; color:var(--text-muted)">Tự đóng sau <span id="modal-auto-close">8</span>s</div>
    </div>
</div>

@endsection


@push('scripts')
<script>
// ============================================================
// CONFIG
// ============================================================
const BINANCE_API = 'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const USER_BALANCE = parseFloat('{{ auth()->user()->balance_point }}');

let currentPrice = 0;
let prevPrice = 0;
let selectedBetType = null;
let sessionEndTime = @json($activeSession?->end_time);
let sessionId = @json($activeSession?->id);
let userBalance = USER_BALANCE;
let hasActiveBet = false;

// ============================================================
// 1. FETCH BTC PRICE FROM BINANCE EVERY SECOND
// ============================================================
async function fetchPrice() {
    try {
        const resp = await fetch(BINANCE_API, { cache: 'no-store' });
        const data = await resp.json();
        const newPrice = parseFloat(data.price);
        updatePriceDisplay(newPrice);
        currentPrice = newPrice;
    } catch (e) {
        // fail silently
    }
}

function updatePriceDisplay(newPrice) {
    const el = document.getElementById('btc-price');
    const changeEl = document.getElementById('change-value');
    const iconEl = document.getElementById('change-icon');

    const diff = newPrice - (prevPrice || newPrice);
    const formatted = '$' + newPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    el.textContent = formatted;

    if (diff > 0) {
        el.className = 'btc-price up';
        iconEl.textContent = '▲';
        changeEl.textContent = '+$' + Math.abs(diff).toFixed(2);
        changeEl.style.color = '#10b981';
        iconEl.style.color = '#10b981';
    } else if (diff < 0) {
        el.className = 'btc-price down';
        iconEl.textContent = '▼';
        changeEl.textContent = '-$' + Math.abs(diff).toFixed(2);
        changeEl.style.color = '#ef4444';
        iconEl.style.color = '#ef4444';
    }

    prevPrice = newPrice;
}

// ============================================================
// 2. SESSION COUNTDOWN TIMER
// ============================================================
let isResolvingSession = false;

function updateTimer() {
    if (!sessionEndTime) return;

    const now = new Date().getTime();
    // Carbon serializes to ISO 8601 UTC format - parse directly, no timezone manipulation needed
    const endStr = typeof sessionEndTime === 'string'
        ? sessionEndTime.replace(' ', 'T').replace(/\+07:00$/, '') // handle both formats
        : sessionEndTime;
    const end = new Date(endStr).getTime();
    if (isNaN(end)) return; // safety guard
    const diff = Math.max(0, Math.floor((end - now) / 1000));

    const mins = Math.floor(diff / 60);
    const secs = diff % 60;

    const timerEl = document.getElementById('countdown');

    if (diff === 0 || isResolvingSession) {
        // Hiển thị trạng thái đang chốt
        timerEl.textContent = '⏳ Đang chốt...';
        timerEl.className = 'session-timer urgent';
        document.getElementById('progress-bar').style.width = '0%';

        if (!isResolvingSession) {
            isResolvingSession = true;
            // Polling mỗi 2 giây cho đến khi nhận được phiên mới
            pollForNewSession();
        }
        return;
    }

    timerEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    timerEl.className = 'session-timer' + (diff <= 10 ? ' urgent' : '');

    // Progress bar (60 seconds = 100%)
    const totalDuration = 60;
    const pct = Math.max(0, Math.min(100, (diff / totalDuration) * 100));
    document.getElementById('progress-bar').style.width = pct + '%';
}

async function pollForNewSession() {
    const bettedSessionId = sessionId; // Track phiên đang cược
    const userHadBet = hasActiveBet;
    let attempts = 0;
    const maxAttempts = 30;

    const poll = async () => {
        if (attempts++ > maxAttempts) {
            isResolvingSession = false;
            return;
        }

        try {
            const resp = await fetch('/api/current-session', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const data = await resp.json();

            // Server đã tạo phiên MỚI → phiên cũ đã chốt xong
            if (data.session && data.session.id !== bettedSessionId) {
                isResolvingSession = false;
                await applyNewSession(data);

                if (userHadBet) {
                    // Lấy kết quả bet ngay lập tức
                    const histResp = await fetch('/api/my-bets', {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    const histData = await histResp.json();

                    if (histData.bets) {
                        // Render bảng lịch sử
                        const tbody = document.getElementById('bets-table');
                        tbody.innerHTML = histData.bets.map(bet => {
                            const typeClass = bet.bet_type === 'long' ? 'text-long' : 'text-short';
                            const typeLabel = bet.bet_type === 'long' ? '▲ LONG' : '▼ SHORT';
                            let statusBadge = '<span class="badge badge-warning">Chờ kết quả</span>';
                            let profitText = '<span style="color:#9ca3af">Chờ...</span>';
                            if (bet.status === 'won') {
                                statusBadge = '<span class="badge badge-success">Thắng 🎉</span>';
                                profitText = `<span style="color:#10b981">+${bet.profit} PT</span>`;
                            } else if (bet.status === 'lost') {
                                statusBadge = '<span class="badge badge-danger">Thua</span>';
                                profitText = `<span style="color:#ef4444">-${bet.bet_amount} PT</span>`;
                            }
                            return `<tr class="bet-row-${bet.status}"><td>${bet.id}</td><td>#${bet.session_id}</td><td class="${typeClass}">${typeLabel}</td><td>${bet.bet_amount} PT</td><td>${statusBadge}</td><td>${profitText}</td><td style="color:var(--text-muted)">${bet.created_at}</td></tr>`;
                        }).join('');

                        // Show modal ngay khi có kết quả
                        const myOldBet = histData.bets.find(b => String(b.session_id) === String(bettedSessionId));
                        if (myOldBet && myOldBet.status !== 'pending') {
                            showResultModal(myOldBet);
                        } else {
                            showToast('🎯 Phiên mới bắt đầu! Hãy đặt cược.', 'info');
                        }

                        if (histData.balance) {
                            userBalance = parseFloat(histData.balance.replace(/,/g, ''));
                            updateNavBalance(histData.balance);
                            document.getElementById('balance-display').textContent = histData.balance;
                        }
                    }
                } else {
                    await refreshBetHistory();
                    showToast('🎯 Phiên mới bắt đầu! Hãy đặt cược.', 'info');
                }
                return;
            }
        } catch (e) {}

        setTimeout(poll, 2000);
    };

    setTimeout(poll, 1500);
}

// ============================================================
// SHOW WIN / LOSE RESULT MODAL
// ============================================================
let resultModalTimer = null;

function showResultModal(bet) {
    const modal = document.getElementById('result-modal');
    const box   = document.getElementById('result-modal-box');
    const isWon = bet.status === 'won';

    document.getElementById('result-icon').textContent    = isWon ? '🏆' : '💸';
    document.getElementById('result-title').textContent   = isWon ? 'THẮNG RỒI!' : 'THUA PHIÊN NÀY';
    document.getElementById('result-title').style.color   = isWon ? '#10b981' : '#ef4444';

    const betTypeLabel = bet.bet_type === 'long' ? '▲ LONG' : '▼ SHORT';
    document.getElementById('result-subtitle').textContent = `Bạn đặt ${betTypeLabel} · ${bet.bet_amount} PT`;

    const profitBox = document.getElementById('result-profit-box');
    const profitAmt = document.getElementById('result-profit-amount');
    const profitLbl = document.getElementById('result-profit-label');

    if (isWon) {
        profitBox.style.background = 'rgba(16,185,129,0.12)';
        profitBox.style.border     = '1px solid rgba(16,185,129,0.35)';
        profitLbl.textContent      = '💰 Nhận về';
        profitAmt.textContent      = '+' + bet.profit + ' PT';
        profitAmt.style.color      = '#10b981';
    } else {
        profitBox.style.background = 'rgba(239,68,68,0.10)';
        profitBox.style.border     = '1px solid rgba(239,68,68,0.30)';
        profitLbl.textContent      = '❌ Mất';
        profitAmt.textContent      = '-' + bet.bet_amount + ' PT';
        profitAmt.style.color      = '#ef4444';
    }

    // Giá mở / đóng nếu API trả về
    document.getElementById('result-start-price').textContent = bet.start_price ? '$' + bet.start_price : '—';
    document.getElementById('result-end-price').textContent   = bet.end_price   ? '$' + bet.end_price   : '—';
    const dirEl = document.getElementById('result-direction-arrow');
    dirEl.textContent   = bet.bet_type === 'long' ? '▲ Giá tăng' : '▼ Giá giảm';
    dirEl.style.color   = bet.bet_type === 'long' ? '#10b981' : '#ef4444';

    // Hiển thị modal với animation
    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        box.style.transform  = 'scale(1)';
        box.style.opacity    = '1';
        box.style.border     = isWon ? '1px solid rgba(16,185,129,0.5)' : '1px solid rgba(239,68,68,0.4)';
        box.style.boxShadow  = isWon ? '0 0 60px rgba(16,185,129,0.3)' : '0 0 60px rgba(239,68,68,0.2)';
    });

    if (isWon) launchConfetti();

    // Auto-close countdown
    let secs = 8;
    document.getElementById('modal-auto-close').textContent = secs;
    resultModalTimer = setInterval(() => {
        secs--;
        const el = document.getElementById('modal-auto-close');
        if (el) el.textContent = secs;
        if (secs <= 0) closeResultModal();
    }, 1000);
}

function closeResultModal() {
    if (resultModalTimer) { clearInterval(resultModalTimer); resultModalTimer = null; }
    const modal = document.getElementById('result-modal');
    const box   = document.getElementById('result-modal-box');
    box.style.transform = 'scale(0.85)';
    box.style.opacity   = '0';
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}

// ============================================================
// CONFETTI EFFECT (pure canvas, no library)
// ============================================================
function launchConfetti() {
    const canvas = document.getElementById('confetti-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width  = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
    const colors = ['#10b981','#6366f1','#f59e0b','#ec4899','#06b6d4','#a3e635'];
    const pieces = Array.from({ length: 90 }, () => ({
        x: Math.random() * canvas.width,
        y: Math.random() * -canvas.height * 0.5,
        w: 6 + Math.random() * 9,
        h: 4 + Math.random() * 6,
        color: colors[Math.floor(Math.random() * colors.length)],
        angle: Math.random() * 360,
        speed: 1.5 + Math.random() * 3,
        spin: (Math.random() - 0.5) * 7,
    }));
    let frame = 0;
    const draw = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pieces.forEach(p => {
            p.y += p.speed; p.angle += p.spin;
            if (p.y > canvas.height) { p.y = -10; p.x = Math.random() * canvas.width; }
            ctx.save();
            ctx.translate(p.x + p.w / 2, p.y + p.h / 2);
            ctx.rotate(p.angle * Math.PI / 180);
            ctx.fillStyle = p.color;
            ctx.globalAlpha = 0.88;
            ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
            ctx.restore();
        });
        if (frame++ < 220) requestAnimationFrame(draw);
        else ctx.clearRect(0, 0, canvas.width, canvas.height);
    };
    draw();
}


// ============================================================
// 3. BET TYPE SELECTION
// ============================================================
function selectBetType(type) {
    selectedBetType = type;
    document.getElementById('btn-long').classList.toggle('active', type === 'long');
    document.getElementById('btn-short').classList.toggle('active', type === 'short');
    updateSubmitButton();
}

function setAmount(val) {
    document.getElementById('bet-amount').value = val;
    updateProfitPreview();
    updateSubmitButton();
}

function setAmountMax() {
    document.getElementById('bet-amount').value = Math.floor(userBalance);
    updateProfitPreview();
    updateSubmitButton();
}

function updateProfitPreview() {
    const amount = parseFloat(document.getElementById('bet-amount').value) || 0;
    const profit = (amount * 1.95).toFixed(0);
    document.getElementById('profit-preview').textContent = `x1.95 = ${parseInt(profit).toLocaleString()} PT`;
    updateSubmitButton();
}

function updateSubmitButton() {
    const amount = parseFloat(document.getElementById('bet-amount').value) || 0;
    const btn = document.getElementById('submit-bet');

    if (hasActiveBet) {
        btn.disabled = true;
        btn.textContent = '✓ Đã đặt cược phiên này';
        return;
    }

    if (!selectedBetType) {
        btn.disabled = true;
        btn.textContent = 'Chọn LONG hoặc SHORT để đặt cược';
        return;
    }

    if (amount <= 0 || amount > userBalance) {
        btn.disabled = true;
        btn.textContent = amount > userBalance ? 'Không đủ Point' : 'Nhập số điểm';
        return;
    }

    const typeLabel = selectedBetType === 'long' ? '▲ LONG' : '▼ SHORT';
    btn.disabled = false;
    btn.textContent = `🚀 Đặt ${typeLabel} - ${amount.toLocaleString()} PT`;
}

// ============================================================
// 4. PLACE BET (AJAX)
// ============================================================
async function placeBet() {
    const amount = parseFloat(document.getElementById('bet-amount').value);

    if (!selectedBetType || !amount || amount <= 0) return;

    const btn = document.getElementById('submit-bet');
    btn.disabled = true;
    btn.textContent = '⏳ Đang đặt cược...';

    try {
        // Re-read CSRF token fresh before each request
        const freshCsrf = document.querySelector('meta[name="csrf-token"]').content;
        const resp = await fetch('/bet', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': freshCsrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                bet_type: selectedBetType,
                bet_amount: amount,
            })
        });

        const data = await resp.json();

        if (data.success) {
            showToast(data.message, 'success');
            userBalance = parseFloat(data.new_balance.replace(/,/g, ''));
            updateNavBalance(data.new_balance);
            document.getElementById('balance-display').textContent = data.new_balance;
            hasActiveBet = true;
            showActiveBetBanner(selectedBetType, amount);
            refreshBetHistory();
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('Lỗi kết nối. Vui lòng thử lại.', 'error');
    }

    updateSubmitButton();
}

// ============================================================
// 5. REFRESH BET HISTORY
// ============================================================
async function refreshBetHistory() {
    try {
        const resp = await fetch('/api/my-bets', { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();

        const tbody = document.getElementById('bets-table');
        if (!data.bets || data.bets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center" style="color:var(--text-muted);padding:2rem">Chưa có lịch sử cược</td></tr>';
            return;
        }

        tbody.innerHTML = data.bets.map(bet => {
            const typeClass = bet.bet_type === 'long' ? 'text-long' : 'text-short';
            const typeLabel = bet.bet_type === 'long' ? '▲ LONG' : '▼ SHORT';

            let statusBadge = '<span class="badge badge-warning">Chờ kết quả</span>';
            let profitText = '<span style="color:#9ca3af">Chờ...</span>';

            if (bet.status === 'won') {
                statusBadge = '<span class="badge badge-success">Thắng 🎉</span>';
                profitText = `<span style="color:#10b981">+${bet.profit} PT</span>`;
            } else if (bet.status === 'lost') {
                statusBadge = '<span class="badge badge-danger">Thua</span>';
                profitText = `<span style="color:#ef4444">-${bet.bet_amount} PT</span>`;
            }

            return `<tr class="bet-row-${bet.status}">
                <td>${bet.id}</td>
                <td>#${bet.session_id}</td>
                <td class="${typeClass}">${typeLabel}</td>
                <td>${bet.bet_amount} PT</td>
                <td>${statusBadge}</td>
                <td>${profitText}</td>
                <td style="color:var(--text-muted)">${bet.created_at}</td>
            </tr>`;
        }).join('');

        // Update balance from latest data
        if (data.balance) {
            userBalance = parseFloat(data.balance.replace(/,/g, ''));
            updateNavBalance(data.balance);
            document.getElementById('balance-display').textContent = data.balance;
        }
    } catch (e) {}
}

// ============================================================
// 6. APPLY NEW SESSION DATA TO UI
// ============================================================
async function applyNewSession(data) {
    sessionId = data.session.id;
    sessionEndTime = data.session.end_time;
    hasActiveBet = !!data.user_bet;

    document.getElementById('sid').textContent = sessionId;
    document.getElementById('form-session-id').textContent = sessionId;
    document.getElementById('session-number').textContent = sessionId;
    document.getElementById('session-start-price').textContent =
        '$' + parseFloat(data.session.start_price).toLocaleString('en-US', { minimumFractionDigits: 2 });
    document.getElementById('session-end-time') &&
        (document.getElementById('session-end-time').textContent = new Date(data.session.end_time).toLocaleTimeString('vi-VN'));

    if (data.user_bet) {
        showActiveBetBanner(data.user_bet.bet_type, data.user_bet.bet_amount);
    } else {
        document.getElementById('active-bet-banner').style.display = 'none';
        // Reset bet form for new session
        selectedBetType = null;
        document.getElementById('btn-long').classList.remove('active');
        document.getElementById('btn-short').classList.remove('active');
        document.getElementById('bet-amount').value = '';
        updateProfitPreview();
    }
    updateSubmitButton();
}

// ============================================================
// 7. REFRESH SESSION (legacy, kept for compatibility)
// ============================================================
async function refreshSession() {
    try {
        const resp = await fetch('/api/current-session', {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        const data = await resp.json();
        await applyNewSession(data);
    } catch (e) {}
}

function showActiveBetBanner(type, amount) {
    const banner = document.getElementById('active-bet-banner');
    const info = document.getElementById('active-bet-info');
    const typeLabel = type === 'long' ? '▲ LONG' : '▼ SHORT';
    const color = type === 'long' ? '#10b981' : '#ef4444';
    info.innerHTML = `Đặt <strong style="color:${color}">${typeLabel}</strong> · <strong>${parseFloat(amount).toLocaleString()} PT</strong>`;
    banner.style.display = 'flex';
}

// ============================================================
// INIT
// ============================================================
// Check if user has bet in current session
@if($activeSession)
(async () => {
    const resp = await fetch('/api/current-session', { headers: { 'Accept': 'application/json' } });
    const data = await resp.json();
    if (data.user_bet) {
        hasActiveBet = true;
        showActiveBetBanner(data.user_bet.bet_type, data.user_bet.bet_amount);
        updateSubmitButton();
    }
})();
@endif

// Start price feed
fetchPrice();
setInterval(fetchPrice, 1000);

// Start timer
updateTimer();
setInterval(updateTimer, 1000);

// Refresh bet history every 5 seconds
setInterval(refreshBetHistory, 5000);
</script>
@endpush
