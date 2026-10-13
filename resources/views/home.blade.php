@extends('layouts.app')

@section('title', 'Конференции.РФ — бронирование площадок')

@section('content')
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow"><span class="dot"></span> БРОНИРОВАНИЕ КОНФЕРЕНЦИОННЫХ ПЛОЩАДОК</span>
        <h1>Подберите зал для <span>вашего события.</span></h1>
        <p class="hero-lead">Выберите формат помещения и дату. Заявка поступит администратору, а её статус можно будет посмотреть в личном кабинете.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route('register') }}">Оформить заявку <span>↗</span></a>
            <a class="button button-outline" href="{{ route('login') }}">Войти в аккаунт</a>
        </div>
        <div class="trust-row"><span class="trust-icon">✓</span><span><b>Заявка онлайн</b><small>Статус всегда под рукой</small></span></div>
    </div>
    <div class="hero-art" aria-label="Иллюстрация конференционного здания">
        <div class="art-kicker">КОНФЕРЕНЦИИ · 2026</div>
        <div class="art-sun"></div>
        <div class="art-building">
            @for ($i = 0; $i < 9; $i++)<span></span>@endfor
        </div>
        <div class="art-card"><span class="art-check">✓</span><span><b>Всё под контролем</b><small>Заявка · Статус · Отзыв</small></span></div>
        <div class="art-index">01 / 03</div>
    </div>
</section>

<section class="section-block">
    <div class="section-head"><div><span class="eyebrow">ВЫБЕРИТЕ ФОРМАТ</span><h2>Подходящее помещение</h2></div><p>Для докладов, совместной работы и показов.</p></div>
    <div class="room-grid">
        <article class="room-card"><div class="room-art room-auditorium"><span>01</span><div class="stage"></div><div class="seats"></div></div><div class="room-caption"><div><h3>Аудитория</h3><p>Доклады и выступления</p></div><span>↗</span></div></article>
        <article class="room-card"><div class="room-art room-cowork"><span>02</span><div class="desk-shape"></div><div class="plant-shape">✳</div></div><div class="room-caption"><div><h3>Коворкинг</h3><p>Работа в небольших группах</p></div><span>↗</span></div></article>
        <article class="room-card"><div class="room-art room-cinema"><span>03</span><div class="screen-shape"></div><div class="cinema-row">▰ ▰ ▰ ▰</div></div><div class="room-caption"><div><h3>Кинозал</h3><p>Показы и презентации</p></div><span>↗</span></div></article>
    </div>
</section>

<section class="how-section">
    <div><span class="eyebrow">КАК ЭТО РАБОТАЕТ</span><h2>От заявки до результата</h2></div>
    <article><span class="how-icon">↗</span><div><b>Заполните заявку</b><p>Укажите помещение, дату и способ оплаты.</p></div></article>
    <article><span class="how-icon">◷</span><div><b>Следите за статусом</b><p>Администратор обновит заявку после рассмотрения.</p></div></article>
    <article><span class="how-icon">✳</span><div><b>Оставьте отзыв</b><p>Форма отзыва появится после завершения мероприятия.</p></div></article>
</section>
@endsection
