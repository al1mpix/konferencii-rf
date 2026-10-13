<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#285779">
    <title>@yield('title', 'Конференции.РФ')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site-header">
    <div class="nav-wrap">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">К</span><span>Конференции<span class="brand-accent">.РФ</span></span></a>
        <nav class="nav-links" aria-label="Основная навигация">
            <a href="{{ route('home') }}">Главная</a>
            @auth
                <a href="{{ route('bookings.index') }}">Мои заявки</a>
                <a class="nav-main" href="{{ route('bookings.create') }}">Создать заявку</a>
                <form method="post" action="{{ route('logout.user') }}" class="inline-form">@csrf<button class="nav-link-button" type="submit">Выйти</button></form>
            @else
                @if (session('is_admin'))
                    <a href="{{ route('admin.index') }}">Панель администратора</a>
                    <form method="post" action="{{ route('logout.admin') }}" class="inline-form">@csrf<button class="nav-link-button" type="submit">Выйти</button></form>
                @else
                    <a href="{{ route('login') }}">Войти</a>
                    <a class="nav-main" href="{{ route('register') }}">Регистрация</a>
                @endif
            @endauth
        </nav>
    </div>
</header>

<main class="page-container">
    @if (session('success'))
        <div class="notice notice-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="notice notice-error" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice notice-error" role="alert">
            <strong>Проверьте введённые данные:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>

<footer class="site-footer">
    <a class="brand footer-brand" href="{{ route('home') }}"><span class="brand-mark">К</span><span>Конференции<span class="brand-accent">.РФ</span></span></a>
    <span>Пространство для важных идей</span>
    <span>© {{ date('Y') }} Учебный проект</span>
</footer>
</body>
</html>
