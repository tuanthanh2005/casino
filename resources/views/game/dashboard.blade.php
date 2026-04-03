@extends('layouts.app')

@section('title', 'Dashboard - AquaHub')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 pt-6 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Welcome Section -->
        <div class="mb-8 animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        Xin Chào, <span class="bg-gradient-to-r from-cyan-400 to-emerald-400 bg-clip-text text-transparent">{{ Auth::user()->name }}</span> 👋
                    </h1>
                    <p class="text-slate-400">Hôm nay là {{ now()->format('d/m/Y') }} - Bắt đầu cuộc phiêu lưu của bạn!</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-slate-400 mb-1">Tổng Balance</div>
                    <div class="text-4xl font-bold text-cyan-400">{{ number_format($balancePoint) }} PT</div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 animate-slide-up" style="animation-delay: 0.1s;">
            <!-- Balance Card -->
            <div class="group relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 hover:border-cyan-500/50 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-slate-400 text-sm font-medium">Số Dư Tài Khoản</span>
                        <div class="w-10 h-10 bg-cyan-500/20 rounded-xl flex items-center justify-center text-cyan-400">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ number_format($balancePoint) }} PT</div>
                    <div class="text-xs text-slate-400 mt-2">Số dư hiện tại của bạn</div>
                </div>
            </div>

            <!-- Winnings Card -->
            <div class="group relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 hover:border-emerald-500/50 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-slate-400 text-sm font-medium">Tổng Thắng</span>
                        <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400">
                            <i class="bi bi-trophy"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ number_format($monthWinTotal) }} PT</div>
                    <div class="text-xs {{ $todayNetProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} mt-2">
                        {{ $todayNetProfit >= 0 ? '↑' : '↓' }} {{ $todayNetProfit >= 0 ? '+' : '' }}{{ number_format($todayNetProfit) }} hôm nay
                    </div>
                </div>
            </div>

            <!-- Active Bets Card -->
            <div class="group relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 hover:border-yellow-500/50 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-slate-400 text-sm font-medium">Cược Đang Chờ</span>
                        <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center text-yellow-400">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $pendingBetsCount }} Cược</div>
                    <div class="text-xs text-yellow-400 mt-2">
                        {{ $pendingBetsCount > 0 ? 'Bạn đang có cược chờ xử lý' : 'Không có cược nào đang chờ' }}
                    </div>
                </div>
            </div>

            <!-- Streak Card -->
            <div class="group relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 hover:border-red-500/50 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-slate-400 text-sm font-medium">Streak Thắng</span>
                        <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center text-red-400">
                            <i class="bi bi-fire"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $currentWinStreak }} Lần</div>
                    <div class="text-xs text-red-400 mt-2">
                        {{ $currentWinStreak > 0 ? 'Chuỗi thắng hiện tại của bạn' : 'Cố lên, bắt đầu chuỗi thắng mới' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Games -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="bi bi-play-circle text-cyan-400"></i> Trò Chơi Nhanh
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 animate-slide-up" style="animation-delay: 0.2s;">
                <!-- Game 1: Prediction -->
                <div class="group relative overflow-hidden rounded-2xl cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 group-hover:border-cyan-500 transition-all duration-300 h-full flex flex-col justify-between">
                        <div>
                            <div class="text-4xl mb-3">📈</div>
                            <h3 class="text-xl font-bold text-white mb-2">Dự Đoán</h3>
                            <p class="text-slate-400 text-sm">Dự đoán Long/Short giá Bitcoin</p>
                        </div>
                        <a href="{{ route('prediction') }}" class="w-full mt-4 px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-semibold transition-colors duration-200 transform hover:scale-105 flex items-center justify-center">
                            Chơi Ngay
                        </a>
                    </div>
                </div>

                <!-- Game 2: Spin -->
                <div class="group relative overflow-hidden rounded-2xl cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500 to-orange-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 group-hover:border-yellow-500 transition-all duration-300 h-full flex flex-col justify-between">
                        <div>
                            <div class="text-4xl mb-3">🎡</div>
                            <h3 class="text-xl font-bold text-white mb-2">Vòng Quay</h3>
                            <p class="text-slate-400 text-sm">May mắn quay bánh xe</p>
                        </div>
                        <a href="{{ route('spin') }}" class="w-full mt-4 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition-colors duration-200 transform hover:scale-105 flex items-center justify-center">
                            Quay Ngay
                        </a>
                    </div>
                </div>

                <!-- Game 3: Dice -->
                <div class="group relative overflow-hidden rounded-2xl cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 group-hover:border-purple-500 transition-all duration-300 h-full flex flex-col justify-between">
                        <div>
                            <div class="text-4xl mb-3">🎲</div>
                            <h3 class="text-xl font-bold text-white mb-2">Tài Xỉu</h3>
                            <p class="text-slate-400 text-sm">Chọn Tài hoặc Xỉu</p>
                        </div>
                        <a href="{{ route('dice') }}" class="w-full mt-4 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-semibold transition-colors duration-200 transform hover:scale-105 flex items-center justify-center">
                            Chơi Ngay
                        </a>
                    </div>
                </div>

                <!-- Game 4: Farm -->
                <div class="group relative overflow-hidden rounded-2xl cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-green-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 group-hover:border-emerald-500 transition-all duration-300 h-full flex flex-col justify-between">
                        <div>
                            <div class="text-4xl mb-3">🌾</div>
                            <h3 class="text-xl font-bold text-white mb-2">Nông Trại</h3>
                            <p class="text-slate-400 text-sm">Trồng cây và thu hoạch</p>
                        </div>
                        <a href="{{ route('farm') }}" class="w-full mt-4 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-semibold transition-colors duration-200 transform hover:scale-105 flex items-center justify-center">
                            Vào Ngay
                        </a>
                    </div>
                </div>

                <!-- Game 5: Kéo Búa Bao -->
                <div class="group relative overflow-hidden rounded-2xl cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 group-hover:border-rose-500 transition-all duration-300 h-full flex flex-col justify-between">
                        <div>
                            <div class="text-4xl mb-3">✊</div>
                            <h3 class="text-xl font-bold text-white mb-2">Kéo Búa Bao</h3>
                            <p class="text-slate-400 text-sm">Nhanh 1 click, có mode BO3</p>
                        </div>
                        <a href="{{ route('rps') }}" class="w-full mt-4 px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg font-semibold transition-colors duration-200 transform hover:scale-105 flex items-center justify-center">
                            Chơi Ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Bets History -->
            <div class="lg:col-span-2 animate-slide-up" style="animation-delay: 0.3s;">
                <div class="bg-slate-800/40 backdrop-blur border border-slate-700 rounded-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 backdrop-blur border-b border-slate-700 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="bi bi-clock-history text-cyan-400"></i> Lịch Sử Cược Gần Đây
                        </h3>
                        <a href="#" class="text-sm text-cyan-400 hover:text-cyan-300">Xem tất cả</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-900/50 border-b border-slate-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400">Trò Chơi</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-400">Cược</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-400">Kết Quả</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-400">Lợi Nhuận</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400">Thời Gian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                @forelse($recentActivities as $index => $activity)
                                    <tr class="hover:bg-slate-800/30 transition-colors duration-200">
                                        <td class="px-6 py-4 text-sm text-slate-300">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $activity['game'] }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-slate-300">{{ number_format($activity['bet_amount']) }} PT</td>
                                        <td class="px-6 py-4 text-sm text-right"><span class="{{ $activity['won'] ? 'text-emerald-400' : 'text-red-400' }} font-semibold">{{ $activity['won'] ? '✓ Thắng' : '✗ Thua' }}</span></td>
                                        <td class="px-6 py-4 text-sm text-right {{ $activity['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-bold">{{ $activity['profit'] >= 0 ? '+' : '' }}{{ number_format($activity['profit']) }} PT</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ optional($activity['created_at'])->format('d/m H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">Chưa có lịch sử cược nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 animate-slide-up" style="animation-delay: 0.4s;">
                <!-- News/Promotion Card -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 relative overflow-hidden group cursor-pointer hover:border-cyan-500/50 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="text-3xl mb-3">🎁</div>
                        <h4 class="text-lg font-bold text-white mb-2">Promtion Hôm Nay</h4>
                        <p class="text-sm text-slate-400 mb-4">Nhận thêm 50% Point cho 3 cược đầu tiên hôm nay</p>
                        <button class="w-full px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-semibold transition-colors duration-200">
                            Nhận Quà
                        </button>
                    </div>
                </div>

                <!-- Shop Highlight -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 relative overflow-hidden group cursor-pointer hover:border-yellow-500/50 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="text-3xl mb-3">🛍️</div>
                        <h4 class="text-lg font-bold text-white mb-2">Shop Premium</h4>
                        <p class="text-sm text-slate-400 mb-4">Sử dụng Point để đổi lấy nhiều quà tặng hấp dẫn</p>
                        <a href="{{ route('shop') }}" class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition-colors duration-200">
                            Vào Shop
                        </a>
                    </div>
                </div>

                <!-- Quick Deposit -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 backdrop-blur border border-slate-700 rounded-2xl p-6 relative overflow-hidden group cursor-pointer hover:border-emerald-500/50 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="text-3xl mb-3">💳</div>
                        <h4 class="text-lg font-bold text-white mb-2">Nạp Tiền</h4>
                        <p class="text-sm text-slate-400 mb-4">Nạp thêm Point để chơi nhiều hơn</p>
                        <a href="{{ route('payment.deposit') }}" class="w-full px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-semibold transition-colors duration-200">
                            Nạp Ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.6s ease-out;
    }

    .animate-slide-up {
        animation: slide-up 0.6s ease-out;
    }

    html {
        scroll-behavior: smooth;
    }
</style>
@endsection
