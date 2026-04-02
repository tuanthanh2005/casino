@extends('layouts.app')

@section('title', 'AquaHub - Trang Chủ')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">
    
    <!-- HERO SECTION -->
    <div class="relative overflow-hidden pt-20 pb-32">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-cyan-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 0.5s;"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-6">
            <div class="text-center space-y-8 animate-fade-in">
                <!-- Main Title -->
                <div class="space-y-4">
                    <h1 class="text-5xl md:text-7xl font-black bg-gradient-to-r from-cyan-400 via-emerald-400 to-cyan-400 bg-clip-text text-transparent leading-tight">
                        AquaHub
                    </h1>
                    <p class="text-xl md:text-2xl text-slate-400 font-light tracking-wide">
                        Dự đoán giá, chơi game, nhận quà tặng
                    </p>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8 animate-slide-up" style="animation-delay: 0.2s;">
                    <a href="{{ route('game') }}" class="group relative px-8 py-4 bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-lg font-semibold text-white overflow-hidden shadow-lg hover:shadow-cyan-500/50 transition-all duration-300 transform hover:scale-105">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <i class="bi bi-play-fill"></i> Chơi Ngay
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-cyan-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a href="#features" class="px-8 py-4 border-2 border-slate-600 rounded-lg font-semibold text-slate-200 hover:bg-slate-800/50 hover:border-cyan-500 transition-all duration-300">
                        Tìm Hiểu Thêm
                    </a>
                </div>

                <!-- Stats Preview -->
                <div class="grid grid-cols-3 gap-4 pt-12 animate-slide-up" style="animation-delay: 0.4s;">
                    <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-lg p-4">
                        <div class="text-3xl font-bold text-cyan-400">2.5K+</div>
                        <div class="text-sm text-slate-400 mt-1">Người Chơi Tích Cực</div>
                    </div>
                    <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-lg p-4">
                        <div class="text-3xl font-bold text-emerald-400">500M+</div>
                        <div class="text-sm text-slate-400 mt-1">Giải Thưởng Phát Hành</div>
                    </div>
                    <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-lg p-4">
                        <div class="text-3xl font-bold text-yellow-400">24/7</div>
                        <div class="text-sm text-slate-400 mt-1">Hoạt Động Liên Tục</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FEATURES SECTION -->
    <section id="features" class="py-24 relative">
        <div class="max-w-6xl mx-auto px-6">
            <!-- Section Title -->
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Tính Năng Nổi Bật
                </h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">
                    Trải nghiệm game tương tác với công nghệ hiện đại nhất
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="group relative bg-slate-800/40 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:bg-slate-800/60 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Dự Đoán Giá Thông Minh</h3>
                        <p class="text-slate-400 text-sm">Phân tích dữ liệu real-time để đưa ra quyết định dự đoán tốt nhất</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="group relative bg-slate-800/40 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-emerald-500/50 transition-all duration-300 hover:bg-slate-800/60 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="bi bi-dice-5"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Mini Game Vui Nhộn</h3>
                        <p class="text-slate-400 text-sm">Tham gia các trò chơi mini hấp dẫn và thắng thêm phần thưởng</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="group relative bg-slate-800/40 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-yellow-500/50 transition-all duration-300 hover:bg-slate-800/60 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="bi bi-gift"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Trao Đổi Quà Tặng</h3>
                        <p class="text-slate-400 text-sm">Sử dụng Point để trao đổi lấy quà tặng premium độc quyền</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="group relative bg-slate-800/40 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:bg-slate-800/60 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="bi bi-percent"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Bonus Thường Xuyên</h3>
                        <p class="text-slate-400 text-sm">Nhận bonus hàng ngày, tuần, tháng từ các hoạt động tham gia</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="group relative bg-slate-800/40 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-emerald-500/50 transition-all duration-300 hover:bg-slate-800/60 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Bảo Mật Tuyệt Đối</h3>
                        <p class="text-slate-400 text-sm">Hệ thống mã hóa cao cấp bảo vệ dữ liệu và tài khoản của bạn</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="group relative bg-slate-800/40 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-yellow-500/50 transition-all duration-300 hover:bg-slate-800/60 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Hỗ Trợ 24/7</h3>
                        <p class="text-slate-400 text-sm">Đội ngũ support luôn sẵn sàng giúp đỡ bạn mọi lúc, mọi nơi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GAME MODES SECTION -->
    <section class="py-24 relative bg-gradient-to-b from-transparent via-slate-900/50 to-transparent">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Các Chế Độ Chơi
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Prediction Game -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-emerald-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300 opacity-0 group-hover:opacity-100"></div>
                    <div class="relative bg-slate-800/50 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-2">Dự Đoán Long/Short</h3>
                                <p class="text-slate-400">Dự đoán hướng đi của giá Bitcoin trong khoảng thời gian cố định</p>
                            </div>
                            <div class="text-4xl text-cyan-400">📈</div>
                        </div>
                        <div class="flex gap-3">
                            <button class="flex-1 px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-semibold transition-colors duration-200">Long</button>
                            <button class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors duration-200">Short</button>
                        </div>
                    </div>
                </div>

                <!-- Spin Game -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-cyan-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300 opacity-0 group-hover:opacity-100"></div>
                    <div class="relative bg-slate-800/50 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-yellow-500/50 transition-all duration-300">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-2">Vòng Quay May Mắn</h3>
                                <p class="text-slate-400">Quay bánh xe để có cơ hội trúng những phần thưởng khác nhau</p>
                            </div>
                            <div class="text-4xl">🎡</div>
                        </div>
                        <button class="w-full px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105">
                            Quay Ngay
                        </button>
                    </div>
                </div>

                <!-- Dice Game -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/20 to-cyan-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300 opacity-0 group-hover:opacity-100"></div>
                    <div class="relative bg-slate-800/50 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-emerald-500/50 transition-all duration-300">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-2">Tài Xỉu</h3>
                                <p class="text-slate-400">Chọn Tài hoặc Xỉu để dự đoán tổng số điểm xúc xắc</p>
                            </div>
                            <div class="text-4xl">🎲</div>
                        </div>
                        <div class="flex gap-3">
                            <button class="flex-1 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-semibold transition-colors duration-200">Tài</button>
                            <button class="flex-1 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-semibold transition-colors duration-200">Xỉu</button>
                        </div>
                    </div>
                </div>

                <!-- Farm Game -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-yellow-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300 opacity-0 group-hover:opacity-100"></div>
                    <div class="relative bg-slate-800/50 backdrop-blur border border-slate-700 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-2">Nông Trại Ảo</h3>
                                <p class="text-slate-400">Trồng cây, chăm sóc nông trại và thu hoạch lợi nhuận hàng ngày</p>
                            </div>
                            <div class="text-4xl">🌾</div>
                        </div>
                        <button class="w-full px-4 py-2 bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105">
                            Vào Nông Trại
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-transparent to-emerald-500/10"></div>
        
        <div class="relative max-w-3xl mx-auto px-6 text-center space-y-8">
            <h2 class="text-4xl font-bold text-white">
                Sẵn Sàng Để Bắt Đầu?
            </h2>
            <p class="text-xl text-slate-400">
                Tham gia cộng đồng AquaHub ngay hôm nay và bắt đầu kiếm thứ chuẩn của bạn
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-10 py-4 bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-lg font-semibold text-white hover:shadow-lg hover:shadow-cyan-500/50 transition-all duration-300 transform hover:scale-105">
                    Đăng Ký Ngay
                </a>
                <a href="{{ route('login') }}" class="px-10 py-4 border-2 border-slate-600 rounded-lg font-semibold text-slate-200 hover:bg-slate-800/50 hover:border-cyan-500 transition-all duration-300">
                    Đăng Nhập
                </a>
            </div>
        </div>
    </section>

</div>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
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

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.6s ease-out;
    }

    .animate-slide-up {
        animation: slide-up 0.6s ease-out;
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(6, 182, 212, 0.3);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(6, 182, 212, 0.6);
    }
</style>

<script>
    // Smooth reveal on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('[class*="hover:"]').forEach(el => {
        observer.observe(el);
    });

    // Smooth number counter animation
    const counters = document.querySelectorAll('.text-3xl.font-bold');
    
    const animateCounter = (element) => {
        const target = parseFloat(element.textContent);
        const max = target * 100;
        let current = 0;
        const increment = max / 30; // 30 frames
        
        const update = () => {
            current += increment;
            if (current < max) {
                element.textContent = (current / 100).toFixed(1) + (element.textContent.includes('K') ? 'K+' : element.textContent.includes('M') ? 'M+' : '');
                requestAnimationFrame(update);
            }
        };
        
        update();
    };

    window.addEventListener('load', () => {
        counters.forEach(counter => {
            observer.observe(counter);
        });
    });
</script>
@endsection
