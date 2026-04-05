<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aquahub.pro</title>
    <link rel="icon" type="image/png" href="{{ asset('av.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        body { background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .auth-card { width: 100%; max-width: 400px; background: white; padding: 2.5rem; border-radius: 24px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08); border: 1px solid rgba(15, 23, 42, 0.05); }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="text-center mb-5">
            <a href="/" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #020617; font-family: 'Outfit'; font-weight: 800; font-size: 1.25rem;">
                <img src="{{ asset('av.png') }}" alt="" style="height: 36px; border-radius: 8px;">
                <span>AQUAHUB</span>
            </a>
            <h1 class="h4 mt-4 fw-bold mb-1">Welcome back</h1>
            <p class="text-secondary small">Enter your credentials to access your account</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small border-0 mb-4" style="background: #fef2f2; color: #991b1b;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-uppercase opacity-75">Email address</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required autofocus style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8125rem;">
            </div>
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label small fw-bold text-uppercase opacity-75">Password</label>
                    <a href="#" class="small text-primary text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot?</a>
                </div>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8125rem;">
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small opacity-75" for="remember">Stay logged in for 30 days</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 mb-4" style="border-radius: 12px; font-weight: 700;">Sign in to account</button>
            
            <p class="text-center small text-secondary mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold">Sign up free</a></p>
        </form>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">🔑</div>
                        <h5 class="fw-bold h4">Forgot Password?</h5>
                        <p class="text-secondary small">Provide your account details and our admin will reach out via chat or email to verify your identity and reset your password manually.</p>
                    </div>

                    <form action="{{ route('password.manual_reset') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="John Doe" style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Account Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="youraccount@email.com" style="background: #f1f5f9; border: none; padding: 0.75rem 1rem; border-radius: 12px;">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3" style="border-radius: 12px;">Request Manual Reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
