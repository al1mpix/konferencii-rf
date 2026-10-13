@extends('layouts.app')

@section('title', 'Панель администратора — Конференции.РФ')

@section('content')
<section class="page-heading dashboard-heading">
    <div><span class="eyebrow">УПРАВЛЕНИЕ ПОРТАЛОМ</span><h1>Заявки пользователей</h1><p>Поиск, фильтрация и изменение статусов мероприятий.</p></div>
    <div class="total-box"><b>{{ $totalCount }}</b><span>заявок всего</span></div>
</section>

<form class="filter-bar" method="get" action="{{ route('admin.index') }}">
    <div class="filter-search"><label for="q">Поиск</label><input id="q" name="q" value="{{ $search }}" placeholder="ФИО, логин, email или помещение"></div>
    <div><label for="status">Статус</label><select id="status" name="status"><option value="">Все статусы</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected($selectedStatus === $status)>{{ $status }}</option>@endforeach</select></div>
    <button class="button button-primary" type="submit">Применить</button>
    <a class="button button-outline" href="{{ route('admin.index') }}">Сбросить</a>
</form>

@if ($bookings->isEmpty())
    <div class="empty-state"><span>⌕</span><h2>Заявки не найдены</h2><p>Измените параметры поиска или фильтр.</p></div>
@else
    <div class="admin-list">
        @foreach ($bookings as $booking)
            <article class="admin-card">
                <div class="card-topline"><span class="reference">ЗАЯВКА №{{ str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT) }}</span><span class="status-pill status-{{ $booking->status === 'Завершено' ? 'done' : ($booking->status === 'Мероприятие назначено' ? 'scheduled' : 'new') }}">{{ $booking->status }}</span></div>
                <h2>{{ $booking->user->full_name }}</h2>
                <p class="muted-note">{{ '@'.$booking->user->login }} · {{ $booking->user->email }}</p>
                <div class="booking-info-grid">
                    <div><small>Помещение</small><b>{{ $booking->room }}</b></div>
                    <div><small>Дата</small><b>{{ $booking->event_date->format('d.m.Y') }}</b></div>
                    <div><small>Оплата</small><b>{{ $booking->payment }}</b></div>
                </div>
                @if ($booking->review)<div class="review-saved"><b>Отзыв пользователя</b><p>{{ $booking->review }}</p></div>@endif
                <form class="status-form" method="post" action="{{ route('admin.status.update', $booking) }}">
                    @csrf @method('PATCH')
                    <div><label for="status-{{ $booking->id }}">Изменить статус</label><select id="status-{{ $booking->id }}" name="status">@foreach ($statuses as $status)<option value="{{ $status }}" @selected($booking->status === $status)>{{ $status }}</option>@endforeach</select></div>
                    <button class="button button-outline" type="submit">Сохранить</button>
                </form>
            </article>
        @endforeach
    </div>
    <div class="pagination-wrap">{{ $bookings->links() }}</div>
@endif
@endsection
