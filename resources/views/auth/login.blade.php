<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aquahub.pro Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="card" style="width: 100%; max-width: 400px; padding: 2.5rem; margin: 1rem; border: none; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.5rem; font-weight: 900; color: #0284c7;">AQUAHUB<span style="color: #64748b;">.PRO</span></h1>
            <p style="color: #64748b; font-size: 0.8125rem; margin-top: 0.5rem;">Access the admin dashboard.</p>
        </div>

        <form method="POST" action="/login">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; font-size: 0.75rem; color: #475569; margin-bottom: 0.5rem; text-transform: uppercase;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;" required autofocus>
                @error('email') <span style="color: #ef4444; font-size: 0.7rem; margin-top: 0.5rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; font-size: 0.75rem; color: #475569; margin-bottom: 0.5rem; text-transform: uppercase;">Password</label>
                <input type="password" name="password" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;" required>
            </div>

            <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="font-size: 0.8125rem; color: #64748b; cursor: pointer;">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem; font-weight: 700;">Sign In</button>
        </form>

        <p style="text-align: center; font-size: 0.75rem; color: #94a3b8; margin-top: 2rem;">© {{ date('Y') }} Aquahub.pro Admin Portal</p>
    </div>
</body>
</html>
