@extends('layouts.app')

@section('title', 'Регистрация — Конференции.РФ')

@section('content')
<section class="auth-layout register-layout">
    <div class="auth-intro">
        <span class="eyebrow">НАЧНИТЕ ЗДЕСЬ</span>
        <h1>Ваше следующее событие начинается <span>здесь.</span></h1>
        <p>Создайте аккаунт, чтобы отправлять заявки, отслеживать их статус и делиться впечатлениями после мероприятия.</p>
        <div class="intro-stat"><b>5 минут</b><span>чтобы начать планирование</span></div>
    </div>
    <div class="form-card">
        <span class="eyebrow">НОВЫЙ АККАУНТ</span>
        <h2>Создать аккаунт</h2>
        <p class="form-description">Все поля обязательны для заполнения.</p>
        <form method="post" action="{{ route('register.store') }}">
            @csrf
            <label for="full_name">ФИО</label>
            <input id="full_name" name="full_name" value="{{ old('full_name') }}" required autocomplete="name" pattern="[А-Яа-яЁё \-]+" placeholder="Иванов Иван Иванович">
            <label for="login">Логин</label>
            <input id="login" name="login" value="{{ old('login') }}" required minlength="6" maxlength="40" pattern="[A-Za-z0-9]+" autocomplete="username" placeholder="Латиница и цифры">
            <small class="field-help">Минимум 6 символов, только латинские буквы и цифры.</small>
            <label for="phone">Телефон</label>
            <input id="phone" name="phone" value="{{ old('phone') }}" required pattern="8\(\d{3}\)\d{3}-\d{2}-\d{2}" placeholder="8(999)123-45-67">
            <label for="email">Электронная почта</label>
            <input id="email" name="email" value="{{ old('email') }}" type="email" required autocomplete="email" placeholder="name@example.ru">
            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Не менее 8 символов">
            <label for="password_confirmation">Повторите пароль</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" placeholder="Введите пароль ещё раз">
            <button class="button button-primary button-full" type="submit">Зарегистрироваться <span>→</span></button>
        </form>
        <p class="form-foot">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
    </div>
</section>
@endsection
