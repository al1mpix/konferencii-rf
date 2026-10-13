@extends('layouts.app')

@section('title', 'Вход — Конференции.РФ')

@section('content')
<section class="auth-layout">
    <div class="auth-intro">
        <span class="eyebrow">С ВОЗВРАЩЕНИЕМ</span>
        <h1>Продолжим <span>организацию</span> вашей идеи.</h1>
        <p>Войдите, чтобы управлять заявками и планировать предстоящие события.</p>
        <blockquote>«Большие идеи начинаются с правильного места»</blockquote>
    </div>
    <div class="form-card">
        <span class="eyebrow">ЛИЧНЫЙ КАБИНЕТ</span>
        <h2>Войти в аккаунт</h2>
        <p class="form-description">Введите данные, указанные при регистрации.</p>
        <form method="post" action="{{ route('login.store') }}">
            @csrf
            <label for="login">Логин</label>
            <input id="login" name="login" value="{{ old('login') }}" autocomplete="username" required autofocus placeholder="Например, Ivan2026">
            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Введите пароль">
            <label class="check-row"><input type="checkbox" name="remember" value="1"> Запомнить меня</label>
            <button class="button button-primary button-full" type="submit">Войти <span>→</span></button>
        </form>
        <p class="form-foot">Ещё не зарегистрированы? <a href="{{ route('register') }}">Регистрация</a></p>
        <div class="admin-hint"><b>Демонстрационный вход администратора</b><span>Conf2027 / Demo77</span></div>
    </div>
</section>
@endsection
