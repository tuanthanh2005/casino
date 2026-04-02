@extends('layouts.app')

@section('title', 'Cửa hàng - Đổi Point lấy quà')

@push('styles')
    <style>
        .shop-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .shop-header h1 {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .shop-header p {
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        .balance-hero {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .balance-hero .amount {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--accent);
        }

        .balance-hero .label {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .item-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }

        .item-card:hover {
            border-color: rgba(245, 158, 11, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }

        .item-img-wrap {
            width: 100%;
            height: 180px;
            overflow: hidden;
            background: linear-gradient(135deg, #1f2937, #374151);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .item-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .item-card:hover .item-img {
            transform: scale(1.05);
        }

        .item-icon-placeholder {
            font-size: 4rem;
            opacity: 0.3;
        }

        .item-body {
            padding: 1.25rem;
        }

        .item-name {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .item-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 1rem;
            min-height: 2.5rem;
        }

        .item-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .item-price {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--accent);
        }

        .item-price span {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .btn-exchange {
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, var(--accent), #fbbf24);
            color: #000;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-exchange:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-exchange:disabled {
            background: var(--bg-card2);
            color: var(--text-muted);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* MY REQUESTS */
        .my-requests-section {
            margin-top: 3rem;
        }

        .my-requests-section h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    /* ── MOBILE APP STYLE ── */
    @media (max-width: 768px) {
        .shop-header { display: none !important; } /* Replaced by status bar */
        
        .balance-hero {
            padding: 1rem !important;
            border-radius: 12px !important;
            margin-bottom: 1rem !important;
        }
        .balance-hero .amount { font-size: 1.8rem !important; }

        .items-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.75rem !important;
            margin-bottom: 2rem !important;
        }
        .item-card { border-radius: 12px !important; }
        .item-img-wrap { height: 120px !important; }
        .item-icon-placeholder { font-size: 2.5rem !important; }
        .item-body { padding: 0.75rem !important; }
        .item-name { font-size: 0.85rem !important; margin-bottom: 0.25rem !important; }
        .item-desc { font-size: 0.7rem !important; min-height: auto !important; margin-bottom: 0.5rem !important; }
        .item-price { font-size: 1rem !important; }
        .btn-exchange { width: 100% !important; padding: 0.4rem !important; font-size: 0.75rem !important; margin-top: 0.5rem !important; }
        .item-footer { display: block !important; }

        .my-requests-section h2 { font-size: 1.1rem !important; }
    }
    </style>
@endpush

@section('content')
<div class="page-enter">
    <!-- Shop Header -->
    <div class="shop-header">
        <h1>🎁 Cửa Hàng Phần Thưởng</h1>
        <p>Đổi Point tích lũy lấy tài khoản Premium yêu thích</p>
    </div>

    <!-- Balance Hero -->
    <div class="balance-hero">
        <div class="amount" id="shop-balance">{{ number_format(auth()->user()->balance_point, 2) }}</div>
        <div class="label"><i class="bi bi-coin"></i> Point của bạn</div>
    </div>

    <!-- Items Grid -->
    <div class="items-grid">
        @forelse($items as $item)
            <div class="item-card">
                <div class="item-img-wrap">
                    @if($item->image)
                        <img class="item-img" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                    @else
                        <div class="item-icon-placeholder">🎁</div>
                    @endif
                </div>
                <div class="item-body">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-desc">{{ $item->description ?? 'Phần thưởng premium cao cấp cho thành viên xuất sắc.' }}
                    </div>
                    <div class="item-footer">
                        <div class="item-price">
                            {{ number_format($item->point_price, 0) }}
                            <span>PT</span>
                        </div>
                        <button class="btn-exchange" id="btn-exchange-{{ $item->id }}"
                            onclick="exchangeItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->point_price }})"
                            @if(auth()->user()->balance_point < $item->point_price) disabled title="Không đủ điểm" @endif>
                            @if(auth()->user()->balance_point < $item->point_price)
                                Thiếu điểm
                            @else
                                Đổi ngay
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; text-align:center; padding:4rem; color:var(--text-muted)">
                <div style="font-size:3rem; margin-bottom:1rem">🛒</div>
                <div>Chưa có phần thưởng nào. Admin sẽ cập nhật sớm!</div>
            </div>
        @endforelse
    </div>

    <!-- My Exchange Requests -->
    <div class="my-requests-section">
        <h2><i class="bi bi-receipt"></i> Lịch sử đổi quà của bạn</h2>
        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Phần thưởng</th>
                            <th>Điểm đổi</th>
                            <th>Trạng thái</th>
                            <th>Khi duyệt thành công TÀI KHOẢN sẽ hiện ở đây</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myRequests as $req)
                            <tr>
                                <td>{{ $req->id }}</td>
                                <td>{{ $req->rewardItem->name ?? 'N/A' }}</td>
                                <td>{{ number_format($req->points_spent, 0) }} PT</td>
                                <td>{!! $req->status_label !!}</td>
                                <td style="color:var(--text-muted)">{{ $req->admin_note ?? '-' }}</td>
                                <td style="color:var(--text-muted)">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="color:var(--text-muted); padding:2rem">
                                    Chưa có lịch sử đổi quà
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="exchange-modal"
        style="display:none; position:fixed; inset:0; z-index:9900; background:rgba(0,0,0,0.7); display:none; align-items:center; justify-content:center; padding:1rem">
        <div
            style="background:var(--bg-card); border:1px solid var(--border); border-radius:20px; padding:2rem; max-width:420px; width:100%">
            <h3 style="font-size:1.2rem; font-weight:700; margin-bottom:0.5rem">Xác nhận đổi quà</h3>
            <p style="color:var(--text-muted); margin-bottom:1.5rem">
                Bạn sẽ dùng <strong style="color:var(--accent)" id="modal-pts">0</strong> Point để đổi lấy
                <strong id="modal-name">---</strong>. Tiếp tục?
            </p>
            <div style="display:flex; gap:1rem">
                <button onclick="closeModal()" class="btn btn-outline" style="flex:1">Hủy</button>
                <button onclick="confirmExchange()" class="btn btn-success" style="flex:1" id="modal-confirm-btn">
                    Xác nhận đổi
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        let pendingExchangeId = null;
        let userBalance = parseFloat('{{ auth()->user()->balance_point }}');

        function exchangeItem(itemId, name, price) {
            pendingExchangeId = itemId;
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-pts').textContent = parseInt(price).toLocaleString();
            const modal = document.getElementById('exchange-modal');
            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('exchange-modal').style.display = 'none';
            pendingExchangeId = null;
        }

        async function confirmExchange() {
            if (!pendingExchangeId) return;

            const btn = document.getElementById('modal-confirm-btn');
            btn.disabled = true;
            btn.textContent = '⏳ Đang xử lý...';

            try {
                const resp = await fetch(`/shop/exchange/${pendingExchangeId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                const data = await resp.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal();

                    // Update balance
                    const newBal = parseFloat(data.new_balance.replace(/,/g, ''));
                    userBalance = newBal;
                    document.getElementById('shop-balance').textContent = data.new_balance;
                    updateNavBalance(data.new_balance);

                    // Sync with mobile header status bar
                    const mNavBalance = document.getElementById('m-nav-balance');
                    if (mNavBalance) mNavBalance.textContent = data.new_balance.split('.')[0];

                    // Reload to update button states
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (e) {
                showToast('Lỗi kết nối. Vui lòng thử lại.', 'error');
            }

            btn.disabled = false;
            btn.textContent = 'Xác nhận đổi';
        }

        // Close modal on backdrop click
        document.getElementById('exchange-modal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });
    </script>
@endpush