<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'min:6', 'max:40', 'regex:/^[A-Za-z0-9]+$/', Rule::unique('users', 'login')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'full_name' => ['required', 'string', 'max:150', 'regex:/^[А-Яа-яЁё\\s-]+$/u'],
            'phone' => ['required', 'regex:/^8\\(\\d{3}\\)\\d{3}-\\d{2}-\\d{2}$/', Rule::unique('users', 'phone')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ], [
            'login.min' => 'Логин должен содержать не менее 6 символов.',
            'login.regex' => 'В логине разрешены только латинские буквы и цифры.',
            'login.unique' => 'Этот логин уже занят.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
            'full_name.regex' => 'ФИО должно содержать кириллицу, пробелы и дефисы.',
            'phone.regex' => 'Телефон должен быть в формате 8(XXX)XXX-XX-XX.',
            'phone.unique' => 'Этот телефон уже зарегистрирован.',
            'email.unique' => 'Этот email уже зарегистрирован.',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'login' => $validated['login'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('bookings.create')
            ->with('success', 'Аккаунт создан. Теперь можно оформить заявку.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (
            hash_equals((string) config('services.conference_admin.login'), $credentials['login'])
            && hash_equals((string) config('services.conference_admin.password'), $credentials['password'])
        ) {
            Auth::logout();
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);

            return redirect()->route('admin.index');
        }

        if (Auth::attempt([
            'login' => $credentials['login'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('bookings.index'));
        }

        return back()->withErrors([
            'login' => 'Неверный логин или пароль.',
        ])->onlyInput('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Вы вышли из системы.');
    }
}
