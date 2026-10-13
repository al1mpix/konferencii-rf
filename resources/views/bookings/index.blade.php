@extends('layouts.app')

@section('title', 'Мои заявки — Конференции.РФ')

@section('content')
<section class="page-heading dashboard-heading">
    <div><span class="eyebrow">ЛИЧНЫЙ КАБИНЕТ</span><h1>Мои заявки</h1><p>Здравствуйте, {{ auth()->user()->full_name }}! Здесь собраны ваши заявки и их текущие статусы.</p></div>
    <a class="button button-primary" href="{{ route('bookings.create') }}">+ Новая заявка</a>
</section>

@if ($bookings->isEmpty())
    <div class="empty-state"><span>✳</span><h2>Заявок пока нет</h2><p>Создайте первую заявку — это займёт всего пару минут.</p><a class="button button-primary" href="{{ route('bookings.create') }}">Создать заявку</a></div>
@else
    <div class="booking-list">
        @foreach ($bookings as $booking)
            <article class="booking-card">
                <div class="card-topline"><span class="reference">ЗАЯВКА №{{ str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT) }}</span><span class="status-pill status-{{ $booking->status === 'Завершено' ? 'done' : ($booking->status === 'Мероприятие назначено' ? 'scheduled' : 'new') }}">{{ $booking->status }}</span></div>
                <div class="booking-info-grid">
                    <div><small>Помещение</small><b>{{ $booking->room }}</b></div>
                    <div><small>Дата мероприятия</small><b>{{ $booking->event_date->format('d.m.Y') }}</b></div>
                    <div><small>Способ оплаты</small><b>{{ $booking->payment }}</b></div>
                </div>
                @if ($booking->review)
                    <div class="review-saved"><b>Ваш отзыв</b><p>{{ $booking->review }}</p></div>
                @elseif ($booking->isCompleted())
                    <form class="review-form" method="post" action="{{ route('bookings.review', $booking) }}">
                        @csrf
                        <label for="review-{{ $booking->id }}">Поделитесь впечатлениями</label>
                        <textarea id="review-{{ $booking->id }}" name="review" maxlength="1000" minlength="3" required placeholder="Как прошло мероприятие?"></textarea>
                        <button class="button button-outline" type="submit">Оставить отзыв</button>
                    </form>
                @else
                    <p class="muted-note">Отзыв можно оставить после завершения мероприятия.</p>
                @endif
            </article>
        @endforeach
    </div>
@endif
@endsection
