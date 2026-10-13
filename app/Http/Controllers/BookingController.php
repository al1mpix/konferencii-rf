<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Auth::user()->bookings()->latest()->get();

        return view('bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        return view('bookings.create', [
            'rooms' => Booking::ROOMS,
            'payments' => Booking::PAYMENTS,
            'minDate' => now()->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room' => ['required', 'string', 'in:'.implode(',', Booking::ROOMS)],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'payment' => ['required', 'string', 'in:'.implode(',', Booking::PAYMENTS)],
        ], [
            'room.in' => 'Выберите помещение из списка.',
            'event_date.after_or_equal' => 'Дата конференции не может быть в прошлом.',
            'payment.in' => 'Выберите способ оплаты из списка.',
        ]);

        Auth::user()->bookings()->create($validated + ['status' => 'Новая']);

        return redirect()->route('bookings.index')
            ->with('success', 'Заявка отправлена. Текущий статус: «Новая».');
    }

    public function review(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        abort_unless($booking->isCompleted(), 403, 'Отзыв доступен только после завершения мероприятия.');

        $validated = $request->validate([
            'review' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'review.required' => 'Напишите текст отзыва.',
            'review.max' => 'Отзыв не может быть длиннее 1000 символов.',
        ]);

        $booking->update(['review' => $validated['review']]);

        return redirect()->route('bookings.index')->with('success', 'Спасибо за ваш отзыв!');
    }
}
