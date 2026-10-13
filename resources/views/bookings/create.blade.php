@extends('layouts.app')

@section('title', 'Создать заявку — Конференции.РФ')

@section('content')
<section class="page-heading">
    <span class="eyebrow">БРОНИРОВАНИЕ · НОВАЯ ЗАЯВКА</span>
    <h1>Создать заявку</h1>
    <p>Расскажите, какое пространство и когда вам нужно.</p>
</section>
<section class="booking-layout">
    <form class="form-card booking-form" method="post" action="{{ route('bookings.store') }}">
        @csrf
        <h2>Параметры мероприятия</h2>
        <p class="form-description">После отправки заявка поступит администратору.</p>
        <label for="room">Тип помещения</label>
        <select id="room" name="room" required>
            <option value="">Выберите формат</option>
            @foreach ($rooms as $room)<option value="{{ $room }}" @selected(old('room') === $room)>{{ $room }}</option>@endforeach
        </select>
        <label for="event_date">Желаемая дата конференции</label>
        <input id="event_date" type="date" name="event_date" min="{{ $minDate }}" value="{{ old('event_date') }}" required>
        <fieldset class="payment-fieldset">
            <legend>Способ оплаты</legend>
            @foreach ($payments as $payment)
                <label class="payment-option">
                    <input type="radio" name="payment" value="{{ $payment }}" @checked(old('payment') === $payment) required>
                    <span><b>{{ $payment }}</b><small>{{ $payment === 'При очном посещении' ? 'Оплата на месте проведения' : 'Безналичный перевод по СБП' }}</small></span>
                </label>
            @endforeach
        </fieldset>
        <button class="button button-primary button-full" type="submit">Отправить заявку <span>→</span></button>
        <p class="fine-print">Новая заявка получит статус «Новая».</p>
    </form>
    <aside class="booking-aside">
        <span class="aside-symbol">✳</span><h2>Что будет дальше?</h2>
        <div class="step-row"><span>01</span><div><b>Заявка отправлена</b><p>Она сразу появится в личном кабинете.</p></div></div>
        <div class="step-row"><span>02</span><div><b>Рассмотрение</b><p>Администратор проверит данные и обновит статус.</p></div></div>
        <div class="step-row"><span>03</span><div><b>Мероприятие</b><p>После завершения вы сможете оставить отзыв.</p></div></div>
    </aside>
</section>
@endsection
