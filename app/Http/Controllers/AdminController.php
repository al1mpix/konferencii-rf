<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::query()->with('user')->latest();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('room', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($users) use ($search) {
                        $users->where('full_name', 'like', "%{$search}%")
                            ->orWhere('login', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->query('status')) {
            if (in_array($status, Booking::STATUSES, true)) {
                $query->where('status', $status);
            }
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('admin.index', [
            'bookings' => $bookings,
            'statuses' => Booking::STATUSES,
            'search' => (string) $request->query('q'),
            'selectedStatus' => (string) $request->query('status'),
            'totalCount' => Booking::count(),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', Booking::STATUSES)],
        ]);

        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Статус заявки обновлён.');
    }
}
