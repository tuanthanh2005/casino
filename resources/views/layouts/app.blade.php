<!DOCTYPE html>
<html lang='{{ str_replace('_', '-', app()->getLocale()) }}'>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title>@yield('title', 'Aquahub.pro')</title>
<link rel="icon" type="image/png" href="{{ asset('av.png') }}">
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
<link rel='stylesheet' href='{{ asset('css/style.css') }}'>
@stack('styles')
</head>
<body>
<nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
<div class='container'>
<a class='navbar-brand' href='/'>
<img src="{{ asset('av.png') }}" alt="Aquahub Logo" style="height: 32px; width: 32px; border-radius: 6px;">
AQUAHUB
</a>
</div>
</nav>
<main class='py-5'>
@yield('content')
</main>
<footer>
<div class='container text-center pt-5 border-top'>
<p>&copy; {{ date('Y') }} Aquahub.pro</p>
</div>
</footer>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
@stack('scripts')
</body>
</html>
