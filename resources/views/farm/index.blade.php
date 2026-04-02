@extends('layouts.app')
@section('title', '🌾 Nông Trại')

@push('styles')
    <style>
        /* ── LAYOUT ── */
        .farm-wrap {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 1.5rem;
            align-items: start;
        }

        @media(max-width:1100px) {
            .farm-wrap {
                grid-template-columns: 1fr 350px;
            }
        }

        @media(max-width:950px) {
            .farm-wrap {
                grid-template-columns: 1fr;
            }
        }

        /* ── PLOT GRID ── */
        .plot-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }

        @media(max-width:600px) {
            .plot-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .plot-card {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 0.9rem 0.6rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .plot-card:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .plot-card.ripe {
            border-color: #10b981;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.25);
            animation: ripePulse 2s ease-in-out infinite;
        }

        .plot-card.dead {
            border-color: rgba(239, 68, 68, 0.4);
            opacity: 0.65;
        }

        .plot-card.empty {
            border-style: dashed;
            opacity: 0.6;
        }

        .plot-card.empty:hover {
            opacity: 1;
            border-color: var(--primary);
        }

        @keyframes ripePulse {

            0%,
            100% {
                box-shadow: 0 0 8px rgba(16, 185, 129, 0.2)
            }

            50% {
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.5)
            }
        }

        .plot-num {
            position: absolute;
            top: 6px;
            left: 8px;
            font-size: 0.65rem;
            color: var(--text-muted);
        }

        .plot-emoji {
            font-size: 2.4rem;
            margin: 0.3rem 0;
        }

        .plot-name {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 0.35rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        /* Progress bar */
        .prog-bar {
            width: 100%;
            height: 5px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 0.3rem;
        }

        .prog-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, #6366f1, #10b981);
            transition: width 1s linear;
        }

        .prog-fill.dead {
            background: #ef4444;
        }

        .plot-timer {
            font-size: 0.68rem;
            color: var(--text-muted);
            font-family: monospace;
        }

        .plot-badges {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 0.3rem;
        }

        .plant-btn {
            padding: 0.3rem 0.65rem;
            border-radius: 8px;
            border: none;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
        }

        .plant-btn.water-btn {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .plant-btn.water-btn:hover:not(:disabled) {
            background: #3b82f6;
            color: #fff;
        }

        .plant-btn.harvest-btn {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            animation: harvestPulse 1s ease-in-out infinite;
        }

        .plant-btn.harvest-btn:hover {
            background: #10b981;
            color: #fff;
        }

        @keyframes harvestPulse {

            0%,
            100% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.05)
            }
        }

        .plant-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        /* ── SIDEBAR TABS ── */
        .side-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .side-tab {
            flex: 1;
            padding: 0.6rem;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-card2);
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s;
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
        }

        .side-tab.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .side-panel {
            display: none;
        }

        .side-panel.active {
            display: block;
        }

        /* ── SHOP ── */
        .seed-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 0.6rem;
            transition: all 0.15s;
        }

        .seed-item:hover {
            border-color: rgba(99, 102, 241, 0.4);
            background: rgba(99, 102, 241, 0.04);
        }

        .seed-emoji-lg {
            font-size: 2rem;
            min-width: 40px;
            text-align: center;
        }

        .seed-info {
            flex: 1;
        }

        .seed-title {
            font-weight: 700;
            font-size: 0.85rem;
        }

        .seed-meta {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 0.1rem;
        }

        .seed-buy-btn {
            padding: 0.4rem 0.8rem;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .seed-buy-btn:hover {
            background: var(--primary-dark);
        }

        /* ── BAG ── */
        .bag-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.75rem;
            background: var(--bg-card2);
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }

        .bag-name {
            font-size: 0.82rem;
            font-weight: 600;
        }

        .bag-qty {
            font-size: 1rem;
            font-weight: 900;
            color: var(--accent);
        }

        /* ── MARKET ── */
        .market-item {
            padding: 0.85rem;
            background: var(--bg-card2);
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 0.6rem;
        }

        .market-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.6rem;
        }

        .market-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .market-input {
            display: flex;
            gap: 0.4rem;
        }

        .market-input input {
            flex: 1;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
            font-family: 'Inter', sans-serif;
        }

        .market-sell-btn {
            padding: 0.4rem 0.8rem;
            background: var(--success);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            white-space: nowrap;
        }

        /* ── NOTIF ── */
        .notif-item {
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            margin-bottom: 0.4rem;
            font-size: 0.8rem;
        }

        .notif-item.ripe {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .notif-item.dead {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        /* ── MODAL ── */
        .farm-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .farm-modal.active {
            display: flex;
        }

        .farm-modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.75rem;
            max-width: 420px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .seed-select-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
            margin-top: 1rem;
        }

        .seed-select-btn {
            padding: 0.75rem;
            border-radius: 12px;
            border: 2px solid var(--border);
            background: transparent;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s;
            color: var(--text);
            font-family: 'Inter', sans-serif;
        }

        .seed-select-btn:hover {
            border-color: var(--primary);
        }

        .seed-select-btn .s-emoji {
            font-size: 1.75rem;
            display: block;
        }

        .seed-select-btn .s-name {
            font-size: 0.75rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }

        .seed-select-btn .s-price {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* ── SPIN MODAL ── */
        #spin-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        #spin-modal.active {
            display: flex;
        }

        .spin-box {
            background: #111827;
            border: 2px solid var(--primary);
            border-radius: 24px;
            padding: 2.5rem;
            width: 320px;
            text-align: center;
            position: relative;
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.4);
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalPop {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .spin-title {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .spin-val-box {
            font-size: 3.5rem;
            font-weight: 900;
            margin: 1rem 0;
            position: relative;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .spin-val {
            text-shadow: 0 0 15px currentColor;
        }

        .spin-total {
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            margin-top: 1rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .spin-total.active {
            opacity: 1;
        }

        .luck-glow {
            position: absolute;
            inset: -20px;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            opacity: 0.1;
            border-radius: 50%;
            z-index: -1;
            animation: luckPulse 1.5s infinite alternate;
        }

        @keyframes luckPulse {
            0% { opacity: 0.05; transform: scale(1); }
            100% { opacity: 0.2; transform: scale(1.1); }
        }

        /* ── MOBILE APP STYLE ── */
        @media (max-width: 768px) {
            .farm-wrap {
                display: block !important;
                margin-top: 2.5rem !important; /* Space for floating header */
            }
            .farm-wrap > div:last-child {
                display: block !important;
                margin-top: 1rem;
            }

            /* Keep sidebar cards visible on mobile */
            #panel-market,
            #panel-shop,
            #panel-bag {
                display: block !important;
                background: var(--bg-card) !important;
                border: 1px solid var(--border) !important;
                box-shadow: none !important;
            }

            #panel-market .card-header,
            #panel-shop .card-header,
            #panel-bag .card-header {
                display: flex !important;
            }

            #panel-market .card-body,
            #panel-shop .card-body,
            #panel-bag .card-body {
                padding: 0.75rem !important;
            }
            
            #panel-farm.active, #panel-shop-mobile.active, #panel-bag-mobile.active, #panel-market-mobile.active {
                display: block !important;
            }
            
            .side-panel {
                animation: panelFade 0.3s ease;
            }
            @keyframes panelFade { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

            .plot-grid {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 0.4rem !important;
            }
            .plot-card {
                min-height: 110px !important;
                padding: 0.5rem 0.3rem !important;
                border-radius: 12px !important;
            }
            .plot-emoji { font-size: 1.8rem !important; }
            .plot-name { font-size: 0.6rem !important; }
            .plot-timer { font-size: 0.55rem !important; }
            .plant-btn { font-size: 0.6rem !important; padding: 0.25rem 0.4rem !important; }

            .desktop-only { display: none !important; }
            
            .card {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
            }
            .card-header { display: none !important; }
            .card-body { padding: 0.5rem 0 !important; }
            
            .side-tabs { display: none !important; } /* Hidden, replaced by bottom nav */
        }
    </style>
@endpush

@section('content')
    <div class="desktop-only">
        <div
            style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem">
            <div>
                <h1 style="font-size:1.75rem; font-weight:900; margin:0">
                    🌾 Nông Trại
                </h1>
                <p style="color:var(--text-muted); margin:0.25rem 0 0; font-size:0.85rem">Trồng cây · Thu hoạch · Bán kiếm PT
                </p>
            </div>
            <div style="display:flex; align-items:center; gap:1rem">
                <div id="balance-display"
                    style="background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:0.5rem 1rem; font-weight:800; font-size:0.9rem">
                    💰 {{ number_format((float) auth()->user()->balance_point, 0) }} PT
                </div>
                {{-- Rules Modal Button --}}
                <button onclick="openRulesModal()"
                    style="background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:0.5rem 0.85rem; cursor:pointer; color:var(--text); font-family:'Inter',sans-serif; font-size:1.1rem"
                    title="Xem luật chơi">
                    ❓
                </button>
                {{-- Notification Bell --}}
                <button onclick="toggleNotif()"
                    style="position:relative; background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:0.5rem 0.85rem; cursor:pointer; color:var(--text); font-family:'Inter',sans-serif; font-size:1.1rem">
                    🔔
                    <span id="notif-badge" class="badge badge-danger"
                        style="position:absolute; top:-6px; right:-6px; font-size:0.65rem; min-width:18px; text-align:center; {{ $unreadCount === 0 ? 'display:none' : '' }}">{{ $unreadCount }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- NOTIFICATIONS DROPDOWN --}}
    <div id="notif-panel"
        style="display:none; background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:1rem; margin-bottom:1.5rem">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem">
            <strong>🔔 Thông báo</strong>
            <button onclick="readAllNotif()"
                style="font-size:0.75rem; color:var(--primary); background:none; border:none; cursor:pointer; font-family:'Inter',sans-serif">Đánh
                dấu đã đọc</button>
        </div>
        @forelse($notifications as $n)
            <div class="notif-item {{ $n->type }}">
                {{ $n->message }}
                <div style="font-size:0.7rem; color:var(--text-muted); margin-top:0.2rem">{{ $n->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:1rem; color:var(--text-muted)">Không có thông báo mới</div>
        @endforelse
    </div>

    <div class="farm-wrap">
        {{-- ═══════ LEFT: PLOTS & MAIN PANELS ═══════ --}}
        <div>
            {{-- PLOTS (Farm Panel) --}}
            <div id="panel-farm" class="side-panel active">
                <div class="card" id="panel-farm-content" style="margin-bottom:0">
                    <div class="card-header" style="justify-content:space-between">
                        <span>🌱 Ô Đất Của Tôi ({{ count($crops) }}/20 đang dùng)</span>
                        <span style="font-size:0.78rem; color:var(--text-muted)">Click ô trống để trồng</span>
                    </div>
                <div class="card-body">
                    <div class="plot-grid" id="plot-grid">
                        @for($slot = 1; $slot <= 20; $slot++)
                            @php $crop = $crops[$slot] ?? null; @endphp
                            <div class="plot-card {{ $crop ? $crop->status : 'empty' }}" id="slot-{{ $slot }}"
                                data-slot="{{ $slot }}"
                                onclick="handleSlotClick({{ $slot }}, '{{ $crop ? $crop->status : 'empty' }}', {{ $crop ? $crop->id : 'null' }})">
                                <span class="plot-num">#{{ $slot }}</span>

                                @if(!$crop)
                                    <div class="plot-emoji">🏜️</div>
                                    <div class="plot-name" style="color:var(--primary)">+ Trồng cây</div>
                                    <div class="plot-timer">&nbsp;</div>

                                @elseif($crop->isDead())
                                    <div class="plot-emoji" style="filter:grayscale(1)">💀</div>
                                    <div class="plot-name" style="color:#ef4444">Cây đã thối hỏng</div>
                                    <div class="prog-bar">
                                        <div class="prog-fill dead" style="width:100%; background:var(--danger)"></div>
                                    </div>
                                    <div class="plot-timer" id="delete-timer-{{ $slot }}"
                                        data-del-ts="{{ $crop->delete_at?->timestamp }}">
                                        {{ $crop->delete_at ? 'Xóa sau ' . $crop->delete_at->diffForHumans() : 'Sẽ tự động xóa' }}
                                    </div>

                                @elseif($crop->isRipe())
                                    <div class="plot-emoji" style="animation:ripeBounce 0.8s ease-in-out infinite">
                                        {{ $crop->seedType->emoji }}</div>
                                    <div class="plot-name" style="color:#10b981; font-weight:800">✅ Đã Chín!</div>
                                    <div class="prog-bar">
                                        <div class="prog-fill" style="width:100%"></div>
                                    </div>
                                    <div class="plot-timer" style="color:#f59e0b"
                                        data-rot-ts="{{ $crop->ripe_at->copy()->addHours(12)->addMinutes(1)->timestamp }}"
                                        data-slot-label="{{ $slot }}">
                                        Thối sau:
                                        {{ gmdate('H:i:s', max(0, $crop->ripe_at->copy()->addHours(12)->addMinutes(1)->timestamp - now()->timestamp)) }}
                                    </div>
                                    <div class="plot-badges">
                                        <button class="plant-btn harvest-btn"
                                            onclick="event.stopPropagation(); harvestCrop({{ $crop->id }}, {{ $slot }})">
                                            🌾 Thu Hoạch
                                        </button>
                                    </div>

                                @elseif($crop->status === 'growing')
                                    <div class="plot-emoji">{{ $crop->seedType->emoji }}</div>
                                    <div class="plot-name">{{ $crop->seedType->name }}</div>
                                    <div class="prog-bar">
                                        <div class="prog-fill" id="prog-{{ $slot }}" style="width:{{ $crop->progressPercent() }}%">
                                        </div>
                                    </div>
                                    <div class="plot-timer" id="timer-{{ $slot }}" data-ripe-ts="{{ $crop->ripe_at->timestamp }}"
                                        data-planted-ts="{{ $crop->planted_at->timestamp }}" data-crop-id="{{ $crop->id }}">
                                        {{ gmdate('H:i:s', max(0, $crop->secondsUntilRipe())) }}
                                    </div>
                                    <div class="plot-badges" id="badges-{{ $slot }}">
                                        @if($crop->seedType->grow_time_mins >= 20)
                                        <button class="plant-btn water-btn" id="water-btn-{{ $slot }}"
                                            onclick="event.stopPropagation(); waterCrop({{ $crop->id }}, {{ $slot }})"
                                            data-cd-ts="{{ $crop->isWaterOnCooldown() ? $crop->last_watered_at->copy()->addSeconds(600)->timestamp : 0 }}"
                                            data-water-count="{{ $crop->watering_count }}"
                                            data-max-water="{{ $crop->seedType->max_waterings }}" {{ !$crop->canWater() ? 'disabled' : '' }}>
                                            💧 {{ $crop->watering_count }}/{{ $crop->seedType->max_waterings }}
                                        </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- MOBILE INJECTED PANELS (Visible only when active on mobile) --}}
            <div class="side-panel" id="panel-shop-mobile" style="display:none">
                <div class="card"><div class="card-body" id="mobile-shop-target"></div></div>
            </div>
            <div class="side-panel" id="panel-bag-mobile" style="display:none">
                <div class="card"><div class="card-body" id="mobile-bag-target"></div></div>
            </div>
            <div class="side-panel" id="panel-market-mobile" style="display:none">
                <div class="card"><div class="card-body" id="mobile-market-target"></div></div>
            </div>

            </div>
        </div>

        {{-- ═══════ RIGHT: SIDEBAR (CONSOLIDATED) ═══════ --}}
        <div class="farm-sidebar" style="display:flex; flex-direction:column; gap:1.5rem">
            {{-- CHỢ NÔNG SẢN (SELL) --}}
            <div id="panel-market" class="card" style="border-color:var(--primary)">
                <div class="card-header" style="background:rgba(6,182,212,0.1); justify-content:space-between">
                    <span style="font-weight:900; color:var(--primary)">💰 CHỢ NÔNG SẢN</span>
                    <button onclick="refreshMarket()" style="background:none; border:none; color:var(--primary); cursor:pointer"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
                <div class="card-body" style="padding:1rem">
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.75rem">Giá bán theo giá gốc, chỉ random ở vòng quay khi bán</div>
                    <div id="market-list">
                        @php 
                            $bagGrouped = []; 
                            foreach($inventory as $item) {
                                $tid = $item->seed_type_id;
                                if(!isset($bagGrouped[$tid])) {
                                    $bagGrouped[$tid] = [ 'seed' => $item->seedType, 'qty' => 0 ];
                                }
                                $bagGrouped[$tid]['qty'] += $item->quantity;
                            }
                        @endphp
                        @forelse($bagGrouped as $tid => $data)
                            @php 
                                $seed = $data['seed']; $qty = $data['qty'];
                                $price = $marketPrices[$tid] ?? $seed->price_sell_base; 
                                $pct = round(($price / $seed->price_sell_base - 1) * 100, 1); 
                            @endphp
                            <div class="market-item" id="market-{{ $tid }}" style="margin-bottom:0.75rem">
                                <div class="market-header">
                                    <span style="font-size:1.5rem">{{ $seed->emoji }}</span>
                                    <div>
                                        <div style="font-weight:700; font-size:0.85rem">{{ $seed->name }}</div>
                                        <div style="font-size:0.72rem; color:var(--text-muted)">Tổng có: <span class="mkt-qty-{{ $tid }}">{{ $qty }}</span> trái</div>
                                    </div>
                                </div>
                                <div class="market-price-row">
                                    <span style="color:var(--text-muted)">Giá:</span>
                                    <span>
                                        <strong id="mkt-price-{{ $tid }}" style="color:{{ $pct >= 0 ? '#10b981' : '#ef4444' }}">{{ number_format($price, 0) }} PT</strong>
                                        <span id="mkt-pct-{{ $tid }}" style="font-size:0.72rem; color:{{ $pct >= 0 ? '#10b981' : '#ef4444' }}">({{ $pct >= 0 ? '+' : '' }}{{ $pct }}%)</span>
                                    </span>
                                </div>
                                <div class="market-input">
                                    <input type="number" min="1" max="{{ $qty }}" id="sell-qty-{{ $tid }}" placeholder="SL..." oninput="calcSellTotal({{ $tid }}, {{ $price }})">
                                    <button class="market-sell-btn" onclick="doSell({{ $tid }}, {{ $qty }})">Bán</button>
                                </div>
                                <div id="sell-total-{{ $tid }}" style="font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; text-align:right"></div>
                            </div>
                        @empty
                            <div style="text-align:center; padding:1.5rem; color:var(--text-muted)">Thu hoạch trái cây để bán! 🌾</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- CỬA HÀNG (BUY) --}}
            <div id="panel-shop" class="card" style="border-color:var(--accent)">
                <div class="card-header" style="background:rgba(16,185,129,0.1)">
                    <span style="font-weight:900; color:var(--accent)">🌿 CỬA HÀNG HẠT GIỐNG</span>
                </div>
                <div class="card-body" style="padding:1rem; max-height:400px; overflow-y:auto">
                    @foreach($seeds as $seed)
                        <div class="seed-item" style="padding:0.5rem; margin-bottom:0.5rem">
                            <div style="font-size:1.8rem; min-width:40px">{{ $seed->emoji }}</div>
                            <div class="seed-info">
                                <div class="seed-title" style="font-size:0.8rem">{{ $seed->name }}</div>
                                <div class="seed-meta" style="font-size:0.65rem">
                                    ⏱ {{ $seed->grow_time_text }} | 💰 ~{{ number_format($seed->price_sell_base, 0) }} PT
                                </div>
                            </div>
                            <button class="seed-buy-btn" style="padding:0.3rem 0.6rem; font-size:0.7rem; white-space:nowrap" onclick="openBulkBuyModal({{ $seed->id }}, '{{ $seed->name }}', '{{ $seed->emoji }}', {{ $seed->price_buy }})">
                                {{ number_format($seed->price_buy, 0) }} PT
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- TÚI ĐỒ (INV) --}}
            <div id="panel-bag" class="card">
                <div class="card-header">
                    <span style="font-weight:900">🎒 TÚI ĐỒ</span>
                </div>
                <div class="card-body" style="padding:0.75rem">
                    <div id="bag-list">
                        @forelse($inventory as $inv)
                            <div class="bag-item" style="padding:0.5rem" data-inv-id="{{ $inv->id }}" data-inv-exp="{{ $inv->expires_at?->timestamp }}">
                                <div style="display:flex; align-items:center; gap:0.5rem">
                                    <span style="font-size:1.4rem">{{ $inv->seedType->emoji }}</span>
                                    <div>
                                        <div class="bag-name" style="font-size:0.75rem">{{ $inv->seedType->name }}</div>
                                        <div class="bag-timer" style="font-size:0.65rem; color:#f59e0b" id="inv-timer-{{ $inv->id }}">
                                            Thối sau: {{ $inv->expires_at ? gmdate('H:i:s', max(0, $inv->expires_at->timestamp - now()->timestamp)) : '—' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="bag-qty">×{{ $inv->quantity }}</div>
                            </div>
                        @empty
                            <div style="text-align:center; padding:1.5rem; color:var(--text-muted); font-size:0.8rem">Túi đồ trống 📦</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- LỊCH SỬ --}}
            <div class="card" style="border-color:var(--border)">
                <div class="card-header">📋 LỊCH SỬ GIAO DỊCH</div>
                <div style="max-height:300px; overflow-y:auto; scrollbar-width:thin">
                    <table class="hist-sm" style="width:100%; border-collapse:collapse; font-size:0.75rem">
                        <thead>
                            <tr>
                                <th style="padding:0.5rem">Loại</th>
                                <th>Hạt</th>
                                <th>SL</th>
                                <th>PT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $tx)
                                <tr>
                                    <td style="padding:0.5rem">{{ $tx->type_label }}</td>
                                    <td>{{ $tx->seedType->emoji }} {{ $tx->seedType->name }}</td>
                                    <td>{{ $tx->quantity }}</td>
                                    <td style="color:{{ $tx->type === 'sell_fruit' ? '#10b981' : ($tx->type === 'buy_seed' ? '#ef4444' : 'var(--text-muted)') }}; font-weight:700">
                                        {{ $tx->type === 'sell_fruit' ? '+' . number_format($tx->total_pt, 0) : ($tx->type === 'buy_seed' ? '-' . number_format($tx->total_pt, 0) : '—') }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center; padding:1.5rem; color:var(--text-muted)">Chưa có giao dịch</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ PLANT MODAL ═══════ --}}
    <div class="farm-modal" id="plant-modal">
        <div class="farm-modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem">
                <strong style="font-size:1.1rem">🌱 Chọn hạt giống</strong>
                <button onclick="closePlantModal()"
                    style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:var(--text-muted)">✕</button>
            </div>
            <div style="font-size:0.8rem; color:var(--text-muted)" id="modal-slot-info">Trồng vào ô #...</div>
            <div class="seed-select-grid" id="modal-seed-grid">
                @foreach($seeds as $seed)
                    <button class="seed-select-btn" onclick="confirmPlant({{ $seed->id }})">
                        <span class="s-emoji">{{ $seed->emoji }}</span>
                        <div class="s-name">{{ $seed->name }}</div>
                        <div class="s-price">{{ number_format($seed->price_buy, 0) }} PT · {{ $seed->grow_time_text }}</div>
                        <div style="font-size:0.65rem; color:var(--text-muted); margin-top:0.2rem">Bán
                            ~{{ number_format($seed->price_sell_base, 0) }} PT</div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════ BULK BUY MODAL ═══════ --}}
    <div class="farm-modal" id="bulk-buy-modal">
        <div class="farm-modal-box" style="max-width:350px">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem">
                <strong style="font-size:1.1rem">🛒 Mua Hàng Loạt</strong>
                <button onclick="closeBulkBuyModal()"
                    style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:var(--text-muted)">✕</button>
            </div>
            
            <div style="text-align:center; padding: 1rem 0">
                <div id="bulk-emoji" style="font-size:3rem; margin-bottom:0.5rem">🌱</div>
                <div id="bulk-name" style="font-weight:bold; font-size:1.2rem; color:var(--primary)">Hạt Giống</div>
                <div style="font-size:0.85rem; color:var(--text-muted); margin-top:0.2rem">Đơn giá: <span id="bulk-price">0</span> PT</div>
            </div>
            
            <div style="margin: 1rem 0">
                <label style="display:block; margin-bottom:0.5rem; font-size:0.85rem; font-weight:bold">Số lượng hạt cần gieo:</label>
                <input type="number" id="bulk-qty" value="1" min="1" max="20" oninput="calcBulkTotal()" 
                    style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:1.2rem; text-align:center; font-weight:bold">
            </div>

            <div style="background:var(--bg); padding:1rem; border-radius:8px; border:1px solid var(--border)">
                <div style="display:flex; justify-content:space-between; margin-bottom:0.8rem; font-size:0.9rem">
                    <span>Tổng tiền:</span>
                    <strong style="color:var(--danger); font-size:1.1rem"><span id="bulk-total-cost">0</span> PT</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.9rem; padding-top:0.8rem; border-top:1px dashed var(--border)">
                    <span style="color:var(--text-muted)">Số dư hiện tại:</span>
                    <strong style="color:var(--primary)"><span id="bulk-current-balance">{{ number_format((float) auth()->user()->balance_point, 0) }}</span> PT</strong>
                </div>
            </div>

            <button onclick="confirmBulkBuy()" id="btn-confirm-bulk"
                style="width:100%; padding:1rem; background:var(--primary); color:#fff; border:none; border-radius:8px; font-weight:bold; font-size:1rem; margin-top:1.5rem; cursor:pointer; transition:all 0.2s">
                💸 Thanh toán & Trồng
            </button>
        </div>
    </div>

    {{-- SPIN MODAL --}}
    <div id="spin-modal">
        <div class="spin-box">
            <div class="luck-glow"></div>
            <div class="spin-title">🎰 Đang tính may mắn...</div>
            <div class="spin-val-box">
                <div class="spin-val" id="spin-pct-val" style="color:#6366f1">--%</div>
            </div>
            <div class="spin-total" id="spin-total-val">+0 PT</div>
            <button id="spin-close-btn" class="market-sell-btn" style="width:100%; margin-top:1.5rem; display:none" onclick="closeSpinModal()">Xác Nhận</button>
        </div>
    </div>

    {{-- RULES MODAL --}}
    <div class="farm-modal" id="rules-modal">
        <div class="farm-modal-box">
            <h3 style="margin-top:0; color:var(--primary); text-align:center; font-weight:900"><i
                    class="bi bi-journal-text"></i> Hướng Dẫn Nông Trại</h3>
            <div style="font-size:0.88rem; line-height:1.6; color:var(--text); margin-bottom:1.5rem;">
                <p><strong>1. Trồng & Chăm Sóc:</strong> Mua hạt giống gieo vào ô đất. Tưới nước định kỳ giúp cây lớn nhanh hơn (giảm thời gian thu hoạch).</p>
                <p><strong>2. Rủi ro Thối Hỏng 💀:</strong> 
                    - Cây chín nếu để quá <strong>12 tiếng</strong> ngoài đồng sẽ bị thối.<br>
                    - Nông sản trong túi nếu để quá <strong>12 tiếng</strong> không bán cũng sẽ bị thối và biến mất!
                </p>
                <p><strong>3. Cảnh Báo 5 Phút ⚠️:</strong> Hệ thống sẽ gửi thông báo và phát âm thanh cảnh báo khi nông sản trong túi sắp thối (còn 5 phút). Hãy bán ngay để tránh mất trắng!</p>
                <p><strong>4. Sức Chứa Túi Đồ 🎒:</strong> Kho tối đa chứa <strong>10 trái</strong>. Hãy bán bớt ở Chợ để có chỗ thu hoạch đợt mới.</p>
                <p><strong>5. Chợ Nông Sản 📈📉:</strong> Giá biến động mạnh mỗi <strong>6 tiếng</strong>. Khi bán, bạn sẽ được gieo xúc xắc may mắn với tỷ lệ <strong>4 Win - 6 Loss</strong> (Tối đa lời +20% hoặc lỗ -20%). Hãy thử vận may!</p>
            </div>
            <div style="display:flex; gap:0.5rem; justify-content:space-between">
                <button class="seed-select-btn" onclick="closeRulesModal(0)"
                    style="flex:1; padding:0.6rem; border-color:var(--border); font-size:0.8rem">Đóng</button>
                <button class="seed-select-btn" onclick="closeRulesModal(3)"
                    style="flex:1.5; padding:0.6rem; border-color:var(--primary); color:var(--primary); font-weight:bold; font-size:0.8rem">Đóng
                    3 tiếng</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <style>
        @keyframes ripeBounce {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-3px)
            }
        }
    </style>
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        let currentSlot = null;
        let globalBalance = {{ (float) auth()->user()->balance_point }};
        let globalUnread = {{ (int) $unreadCount }};
        let isFirstTick = true;

        // ── AUDIO ALERT ──
        function playAlertSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'square';
                osc.frequency.setValueAtTime(600, ctx.currentTime);
                osc.frequency.setValueAtTime(800, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            } catch (e) { }
        }

        // ── RULES MODAL ──
        function openRulesModal() {
            document.getElementById('rules-modal').classList.add('active');
        }
        function closeRulesModal(hours = 0) {
            document.getElementById('rules-modal').classList.remove('active');
            if (hours > 0) {
                localStorage.setItem('hide_farm_rules_until', Date.now() + (hours * 60 * 60 * 1000));
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            const hideUntil = localStorage.getItem('hide_farm_rules_until');
            if (!hideUntil || Date.now() > parseInt(hideUntil)) {
                setTimeout(openRulesModal, 300);
            }
        });

        // ── SIDEBAR TABS ──
        function showSideTab(tab, el) {
            // Update panels (Desktop)
            document.querySelectorAll('.side-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.side-tab').forEach(t => t.classList.remove('active'));
            
            const targetPanel = document.getElementById('panel-' + tab);
            if (targetPanel) targetPanel.classList.add('active');
            if (el && el.classList.contains('side-tab')) el.classList.add('active');

            // Update Bottom Nav (Mobile)
            if (window.innerWidth <= 768) {
                document.querySelectorAll('.m-nav-item').forEach(i => i.classList.remove('active'));
                const mNavItem = document.getElementById('m-nav-' + tab);
                if (mNavItem) mNavItem.classList.add('active');
                
                // On mobile, some content might need to shift to the main panel area
                if (tab !== 'farm') {
                    const sourceContent = document.getElementById('panel-' + tab);
                    if (sourceContent) {
                        const targetWrapper = document.getElementById('panel-' + tab + '-mobile');
                        const targetBody = document.getElementById('mobile-' + tab + '-target');
                        if (targetWrapper && targetBody) {
                            const farmPanel = document.getElementById('panel-farm');
                            if (farmPanel) {
                                farmPanel.classList.add('active');
                                farmPanel.style.display = 'block';
                            }

                            targetBody.innerHTML = sourceContent.innerHTML;
                            targetWrapper.classList.add('active');
                            targetWrapper.style.display = 'block';
                            // Hide only farm grid content, keep parent visible for injected panels.
                            const farmContent = document.getElementById('panel-farm-content');
                            if (farmContent) farmContent.style.display = 'none';
                        }
                    }
                } else {
                    const farmPanel = document.getElementById('panel-farm');
                    if (farmPanel) {
                        farmPanel.classList.add('active');
                        farmPanel.style.display = 'block';
                    }

                    const farmContent = document.getElementById('panel-farm-content');
                    if (farmContent) farmContent.style.display = 'block';
                    // Hide mobile helper panels
                    ['shop','bag','market'].forEach(t => {
                        const p = document.getElementById('panel-' + t + '-mobile');
                        if(p) { p.style.display = 'none'; p.classList.remove('active'); }
                    });
                }
            }
        }

        // ── NOTIFICATION ──
        function toggleNotif() {
            const p = document.getElementById('notif-panel');
            p.style.display = p.style.display === 'none' ? '' : 'none';
        }
        async function readAllNotif() {
            await fetch('/farm/notifications/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } });
            
            const b1 = document.getElementById('notif-badge');
            const b2 = document.getElementById('m-notif-badge');
            if (b1) b1.style.display = 'none';
            if (b2) b2.style.display = 'none';
            
            const p = document.getElementById('notif-panel');
            if (p) p.style.display = 'none';
            
            showToast('Đã đọc tất cả thông báo', 'success');
        }

        // ── PLOT CLICK ──
        function handleSlotClick(slot, status, cropId) {
            if (status === 'empty') openPlantModal(slot);
            else if (status === 'ripe') harvestCrop(cropId, slot);
            // growing & dead: click không làm gì, dùng buttons
        }

        // ── PLANT MODAL ──
        function openPlantModal(slot) {
            currentSlot = slot;
            document.getElementById('modal-slot-info').textContent = `Trồng vào ô #${slot}`;
            document.getElementById('plant-modal').classList.add('active');
        }
        function closePlantModal() {
            document.getElementById('plant-modal').classList.remove('active');
            currentSlot = null;
        }

        // MUA HÀNG LOẠT
        let bulkSeedId = null;
        let bulkPrice = 0;

        function openBulkBuyModal(id, name, emoji, price) {
            bulkSeedId = id;
            bulkPrice = price;
            document.getElementById('bulk-name').textContent = name;
            document.getElementById('bulk-emoji').textContent = emoji;
            document.getElementById('bulk-price').textContent = new Intl.NumberFormat().format(price);
            document.getElementById('bulk-qty').value = 1;
            calcBulkTotal();
            document.getElementById('bulk-buy-modal').classList.add('active');
        }

        function closeBulkBuyModal() {
            document.getElementById('bulk-buy-modal').classList.remove('active');
            bulkSeedId = null;
        }

        function calcBulkTotal() {
            let qty = parseInt(document.getElementById('bulk-qty').value) || 0;
            if(qty < 1) { qty = 1; document.getElementById('bulk-qty').value = 1; }
            if(qty > 20) { qty = 20; document.getElementById('bulk-qty').value = 20; }
            let total = qty * bulkPrice;
            document.getElementById('bulk-total-cost').textContent = new Intl.NumberFormat().format(total);
            
            const btn = document.getElementById('btn-confirm-bulk');
            if (total > globalBalance) {
                btn.style.background = 'var(--danger)';
                btn.style.cursor = 'not-allowed';
                btn.innerHTML = '❌ Không đủ số dư';
            } else {
                btn.style.background = 'var(--primary)';
                btn.style.cursor = 'pointer';
                btn.innerHTML = '💸 Thanh toán & Trồng';
            }
        }

        async function confirmBulkBuy() {
            let qty = parseInt(document.getElementById('bulk-qty').value) || 1;
            let total = qty * bulkPrice;
            if (total > globalBalance) {
                showToast("Số dư của bạn không đủ!", 'error');
                return;
            }

            const btn = document.getElementById('btn-confirm-bulk');
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang xử lý...';
            btn.disabled = true;
            btn.style.opacity = '0.7';

            const resp = await fetch('/farm/plant-bulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ seed_type_id: bulkSeedId, quantity: qty })
            });
            const data = await resp.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                updateBalance(data.balance);
                closeBulkBuyModal();
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(data.message, 'error');
                btn.innerHTML = '💸 Thanh toán & Trồng';
                btn.disabled = false;
                btn.style.opacity = '1';
                calcBulkTotal();
            }
        }

        async function confirmPlant(seedId) {
            if (!currentSlot) return;
            const slotToPlant = currentSlot;
            closePlantModal();
            const resp = await fetch('/farm/plant', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ slot: slotToPlant, seed_type_id: seedId })
            });
            const data = await resp.json();
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                updateBalance(data.balance);
                setTimeout(() => location.reload(), 1200);
            }
        }

        // ── WATER ──
        async function waterCrop(cropId, slot) {
            const btn = document.getElementById('water-btn-' + slot);
            if (btn) btn.disabled = true;
            const resp = await fetch(`/farm/water/${cropId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await resp.json();
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                // Update button label
                if (btn) {
                    btn.textContent = `💧 ${data.water_count}/${data.max_water}`;
                    btn.disabled = true;
                    // Re-enable after 10 min
                    setTimeout(() => { btn.disabled = false; }, 600 * 1000);
                }
                pollStatus(); // refresh immediately
            } else {
                if (btn) btn.disabled = false;
            }
        }

        // ── HARVEST ──
        async function harvestCrop(cropId, slot) {
            const resp = await fetch(`/farm/harvest/${cropId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await resp.json();
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1500);
        }

        // ── SELL ──
        function calcSellTotal(seedId, price) {
            const qty = parseInt(document.getElementById('sell-qty-' + seedId).value) || 0;
            const el = document.getElementById('sell-total-' + seedId);
            if (qty > 0) {
                el.textContent = `Dự kiến nhận: ~${Math.floor(qty * price).toLocaleString('vi-VN')} PT 🎲`;
                el.style.color = '#10b981';
            } else {
                el.textContent = '';
            }
        }

        async function doSell(seedId, maxQty) {
            const qtyEl = document.getElementById('sell-qty-' + seedId);
            const qty = parseInt(qtyEl.value) || 0;
            if (qty <= 0) { showToast('Nhập số lượng muốn bán!', 'error'); return; }
            if (qty > maxQty) { showToast(`Chỉ có ${maxQty} trái!`, 'error'); return; }

            // Open Spin Modal
            const modal = document.getElementById('spin-modal');
            const pctVal = document.getElementById('spin-pct-val');
            const totalVal = document.getElementById('spin-total-val');
            const closeBtn = document.getElementById('spin-close-btn');
            
            modal.classList.add('active');
            pctVal.textContent = '--%';
            pctVal.style.color = '#6366f1';
            totalVal.classList.remove('active');
            closeBtn.style.display = 'none';

            // Random flickering effect for 1.2s
            let flickerInt = setInterval(() => {
                const rand = (Math.random() * 10 - 5).toFixed(1);
                pctVal.textContent = (rand >= 0 ? '+' : '') + rand + '%';
                pctVal.style.color = rand >= 0 ? '#10b981' : '#ef4444';
            }, 50);

            try {
                const resp = await fetch('/farm/sell', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ seed_type_id: seedId, quantity: qty })
                });
                const data = await resp.json();
                
                // Keep flickering for at least 1s
                setTimeout(() => {
                    clearInterval(flickerInt);
                    if (data.success) {
                        const luck = data.luck_pct;
                        pctVal.textContent = (luck >= 0 ? '+' : '') + luck + '%';
                        pctVal.style.color = luck >= 0 ? '#10b981' : '#ef4444';
                        totalVal.textContent = '+' + data.total_pt.toLocaleString() + ' PT';
                        totalVal.classList.add('active');
                        closeBtn.style.display = 'block';
                        document.querySelector('.spin-title').textContent = '🎰 Kết quả bán hàng';
                        
                        updateBalance(data.balance);
                        if (data.inventory) {
                            renderBag(data.inventory);
                            renderMarket(data.inventory);
                        }
                    } else {
                        modal.classList.remove('active');
                        showToast(data.message, 'error');
                    }
                }, 1000);

            } catch (e) {
                clearInterval(flickerInt);
                modal.classList.remove('active');
                showToast('Lỗi kết nối máy chủ!', 'error');
            }
        }

        function closeSpinModal() {
            document.getElementById('spin-modal').classList.remove('active');
            document.querySelector('.spin-title').textContent = '🎰 Đang tính may mắn...';
        }

        async function refreshMarket() {
            const resp = await fetch('/farm/market/refresh', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
            const data = await resp.json();
            if (data.prices) {
                Object.entries(data.prices).forEach(([id, p]) => {
                    const priceEl = document.getElementById('mkt-price-' + id);
                    const pctEl = document.getElementById('mkt-pct-' + id);
                    if (priceEl) {
                        priceEl.textContent = Math.floor(p.price).toLocaleString('vi-VN') + ' PT';
                        priceEl.style.color = p.is_profit ? '#10b981' : '#ef4444';
                    }
                    if (pctEl) {
                    const s = p.pct >= 0 ? '+' : '';
                    pctEl.textContent = `(${s}${p.pct}%)`;
                    pctEl.style.color = p.is_profit ? '#10b981' : '#ef4444';
                }
            });
            if (data.expires_at) {
                const el = document.getElementById('market-countdown');
                if (el) el.dataset.marketExp = data.expires_at;
            }
            showToast('Đã cập nhật giá thị trường!', 'success');
        }
        }

        // ── BALANCE ──
        function updateBalance(val) {
            globalBalance = parseFloat(val);
            const fmt = Math.floor(val).toLocaleString('vi-VN');
            
            // Desktop
            const el = document.getElementById('balance-display');
            if (el) el.textContent = '💰 ' + fmt + ' PT';
            
            // Mobile Header
            const mEl = document.getElementById('m-nav-balance');
            if (mEl) mEl.textContent = fmt;
            
            // Bulk Modal
            const bulkEl = document.getElementById('bulk-current-balance');
            if (bulkEl) bulkEl.textContent = fmt;

            // Global Layout Balance (optional, if exists)
            const globalEl = document.getElementById('nav-balance');
            if (globalEl) globalEl.textContent = fmt;
        }

        // ── COUNTDOWN TIMERS (runs every second) ──
        function formatTime(secs) {
            if (secs <= 0) return '00:00:00';
            const h = Math.floor(secs / 3600);
            const m = Math.floor((secs % 3600) / 60);
            const s = secs % 60;
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }

        function tickTimers() {
            const now = Math.floor(Date.now() / 1000);

            // 1. Ripe timers
            document.querySelectorAll('[data-ripe-ts]').forEach(el => {
                const ripe = parseInt(el.dataset.ripeTs);
                const planted = parseInt(el.dataset.plantedTs);
                const secs = Math.max(0, ripe - now);
                const slot = el.id.replace('timer-', '');
                const prog = document.getElementById('prog-' + slot);
                const cropId = el.dataset.cropId;

                if (secs <= 0) {
                    if (!isFirstTick && el.dataset.ripeWarn !== "1") {
                        el.dataset.ripeWarn = "1";
                        playAlertSound();
                        showToast('✅ Đất #' + slot + ' đã chín, mau thu hoạch!', 'success');
                    }

                    if (prog) prog.style.width = '100%';

                    // Auto swap to harvest button
                    const badges = document.getElementById('badges-' + slot);
                    if (badges && !badges.querySelector('.harvest-btn')) {
                        badges.innerHTML = `<button class="plant-btn harvest-btn" onclick="event.stopPropagation(); harvestCrop(${cropId}, ${slot})">🌾 Thu Hoạch</button>`;
                        document.getElementById('slot-' + slot).classList.add('ripe');
                        document.getElementById('slot-' + slot).classList.remove('growing');

                        // Swap timer to rot timer (12 hours + 1 min grace)
                        const rotTs = ripe + (12 * 3600) + 60;
                        el.removeAttribute('data-ripe-ts');
                        el.setAttribute('data-rot-ts', rotTs);
                        el.dataset.slotLabel = slot; // Save slot for rot warning
                        el.style.color = '#f59e0b';
                    }
                } else {
                    el.textContent = formatTime(secs);
                    if (prog && planted && ripe > planted) {
                        const total = ripe - planted;
                        const elapsed = now - planted;
                        const pct = Math.min(100, Math.max(0, (elapsed / total) * 100));
                        prog.style.width = pct + '%';
                    }
                }
            });

            // 1.b Rot timers
            document.querySelectorAll('[data-rot-ts]').forEach(el => {
                const secs = Math.max(0, parseInt(el.dataset.rotTs) - now);

                // 5 minute warning
                if (secs <= 300 && secs > 0 && el.dataset.rotWarn !== "1") {
                    // Only alert if we didn't just load the page with < 5 mins
                    if (!isFirstTick) {
                        playAlertSound();
                        setTimeout(playAlertSound, 400); // multiple beeps
                        showToast('⚠️ Cây ô #' + (el.dataset.slotLabel || '?') + ' sắp thối trong vòng 5 phút nữa!', 'error');
                    }
                    el.dataset.rotWarn = "1";
                }

                if (secs <= 0) {
                    el.textContent = '❌ Đã thối hỏng!';
                    el.style.color = '#ef4444';
                } else {
                    el.textContent = 'Thối sau: ' + formatTime(secs);
                }
            });

            // 1.c Inventory timers (Bag)
            document.querySelectorAll('[data-inv-exp]').forEach(el => {
                const exp = parseInt(el.dataset.invExp);
                const secs = Math.max(0, exp - now);
                const id = el.dataset.invId;
                const timerEl = document.getElementById('inv-timer-' + id);

                // 5 minute warning for inventory
                if (secs <= 300 && secs > 0 && el.dataset.invWarn !== "1") {
                    if (!isFirstTick) {
                        playAlertSound();
                        setTimeout(playAlertSound, 400); 
                        showToast('⚠️ Nông sản trong túi sắp thối sau 5 phút nữa!', 'error');
                    }
                    el.dataset.invWarn = "1";
                }

                if (timerEl) {
                    if (secs <= 0) {
                        timerEl.textContent = '❌ Đã thối hỏng!';
                        timerEl.style.color = '#ef4444';
                    } else {
                        timerEl.textContent = 'Thối sau: ' + formatTime(secs);
                    }
                }
            });

            // 2. Cooldown timers
            document.querySelectorAll('.water-btn').forEach(btn => {
                const cdTs = parseInt(btn.dataset.cdTs) || 0;
                if (cdTs > now) {
                    btn.disabled = true;
                    btn.textContent = `⏳ ${formatTime(cdTs - now)}`;
                } else if (cdTs > 0 && cdTs <= now) {
                    btn.dataset.cdTs = "0";
                    const w = parseInt(btn.dataset.waterCount);
                    const m = parseInt(btn.dataset.maxWater);
                    if (w < m) {
                        btn.disabled = false;
                        btn.textContent = `💧 ${w}/${m}`;
                    }
                }
            });

            isFirstTick = false;
        }
        setInterval(tickTimers, 1000);

        // ── POLLING STATUS every 30s ──
        async function pollStatus() {
            const resp = await fetch('/farm/status', { headers: { 'Accept': 'application/json' } });
            const data = await resp.json();
            if (data.balance !== undefined) updateBalance(data.balance);
            if (data.unread !== undefined) {
                if (data.unread > globalUnread) {
                    playAlertSound();
                    showToast('Bạn có thông báo mới!', 'success');
                }
                globalUnread = data.unread;
                
                const b1 = document.getElementById('notif-badge');
                const b2 = document.getElementById('m-notif-badge');
                
                if (b1) {
                    b1.textContent = data.unread;
                    b1.style.display = data.unread > 0 ? '' : 'none';
                }
                if (b2) {
                    b2.textContent = data.unread;
                    b2.style.display = data.unread > 0 ? 'block' : 'none';
                }
            }
            // Update data-attributes silently to avoid full reload if possible
            data.crops.forEach(c => {
                const card = document.getElementById('slot-' + c.slot);
                if (!card) return;
                const oldStatus = card.classList.contains('growing') ? 'growing'
                    : card.classList.contains('ripe') ? 'ripe'
                        : card.classList.contains('dead') ? 'dead' : 'empty';

                // If status fundamentally changed, reload
                if (c.status !== oldStatus) {
                    // Except if front-end ALREADY marked it as ripe visually
                    if (oldStatus === 'growing' && c.status === 'ripe' && card.classList.contains('ripe')) {
                        // Ignore, we already patched it visually!
                    } else {
                        location.reload();
                    }
                } else if (c.status === 'growing') {
                    const btn = document.getElementById('water-btn-' + c.slot);
                    if (btn) {
                        btn.dataset.cdTs = c.cd_ts;
                        btn.dataset.waterCount = c.water_count;
                        if (!c.can_water && c.cd_ts <= Math.floor(Date.now() / 1000)) btn.disabled = true;
                    }
                    const timer = document.getElementById('timer-' + c.slot);
                    if (timer) {
                        timer.dataset.ripeTs = c.ripe_at_ts;
                        timer.dataset.plantedTs = c.planted_ts;
                    }
                }
            });

            // Update Inventory (Bag & Market)
            if (data.inventory) {
                renderBag(data.inventory);
                renderMarket(data.inventory);
            }
        }

        function renderBag(items) {
            const list = document.getElementById('bag-list');
            if (!list) return;

            // Group by type for cleaner UI
            const grouped = {};
            items.forEach(inv => {
                if (!grouped[inv.seed_type_id]) {
                    grouped[inv.seed_type_id] = { 
                        qty: 0, 
                        name: inv.name, 
                        emoji: inv.emoji, 
                        exp: inv.expires_at_ts,
                        id: inv.id // for timer tracking
                    };
                }
                grouped[inv.seed_type_id].qty += inv.quantity;
                // Keep the earliest expiry
                if (inv.expires_at_ts < grouped[inv.seed_type_id].exp) {
                    grouped[inv.seed_type_id].exp = inv.expires_at_ts;
                    grouped[inv.seed_type_id].id = inv.id;
                }
            });

            const types = Object.keys(grouped);
            if (types.length === 0) {
                list.innerHTML = '<div style="text-align:center; padding:2rem; color:var(--text-muted)">Túi đồ trống 📦</div>';
                return;
            }

            // FULL RE-RENDER for Bag (consistent with Market)
            let html = '';
            types.forEach(tid => {
                const data = grouped[tid];
                html += `
                    <div class="bag-item" data-inv-id="${data.id}" data-inv-exp="${data.exp}">
                        <div style="display:flex; align-items:center; gap:0.5rem">
                            <span style="font-size:1.4rem">${data.emoji}</span>
                            <div>
                                <div class="bag-name">${data.name}</div>
                                <div class="bag-timer" style="font-size:0.7rem; color:#f59e0b" id="inv-timer-${data.id}">
                                    Thối sớm nhất: ...
                                </div>
                            </div>
                        </div>
                        <div class="bag-qty">×${data.qty}</div>
                    </div>`;
            });
            list.innerHTML = html;
        }

        function renderMarket(items) {
            const list = document.getElementById('market-list');
            if (!list) return;

            const grouped = {};
            items.forEach(inv => {
                if (!grouped[inv.seed_type_id]) {
                    grouped[inv.seed_type_id] = { qty: 0, name: inv.name, emoji: inv.emoji };
                }
                grouped[inv.seed_type_id].qty += inv.quantity;
            });

            const types = Object.keys(grouped);
            if (types.length === 0) {
                list.innerHTML = '<div style="text-align:center; padding:1.5rem; color:var(--text-muted)">Thu hoạch trái cây để bán! 🌾</div>';
                return;
            }

            let html = '';
            types.forEach(tid => {
                const data = grouped[tid];
                const oldPriceEl = document.getElementById('mkt-price-' + tid);
                const oldPctEl = document.getElementById('mkt-pct-' + tid);
                
                let priceText = oldPriceEl ? oldPriceEl.innerText : '... PT';
                let priceColor = oldPriceEl ? oldPriceEl.style.color : 'inherit';
                let pctHtml = oldPctEl ? oldPctEl.outerHTML : '';
                
                let rawPrice = parseInt(priceText.replace(/[^\d]/g, '')) || 0;

                html += `
                <div class="market-item" id="market-${tid}" style="margin-bottom:0.75rem">
                    <div class="market-header">
                        <span style="font-size:1.5rem">${data.emoji}</span>
                        <div>
                            <div style="font-weight:700; font-size:0.85rem">${data.name}</div>
                            <div style="font-size:0.72rem; color:var(--text-muted)">Tổng có: <span class="mkt-qty-${tid}">${data.qty}</span> trái</div>
                        </div>
                    </div>
                    <div class="market-price-row">
                        <span style="color:var(--text-muted)">Giá:</span>
                        <span>
                            <strong id="mkt-price-${tid}" style="color:${priceColor}">${priceText}</strong>
                            ${pctHtml}
                        </span>
                    </div>
                    <div class="market-input">
                        <input type="number" min="1" max="${data.qty}" id="sell-qty-${tid}" placeholder="SL..." oninput="calcSellTotal(${tid}, ${rawPrice})">
                        <button class="market-sell-btn" onclick="doSell(${tid}, ${data.qty})">Bán</button>
                    </div>
                    <div id="sell-total-${tid}" style="font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; text-align:right"></div>
                </div>`;
            });
            list.innerHTML = html;
        }

        setInterval(pollStatus, 30000);

        // Close modal on backdrop click
        document.getElementById('plant-modal').addEventListener('click', function (e) {
            if (e.target === this) closePlantModal();
        });
    </script>
@endpush