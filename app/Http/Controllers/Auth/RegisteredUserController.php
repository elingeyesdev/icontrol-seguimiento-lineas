<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'ci' => ['required', 'string', 'max:255', 'unique:users,ci'],
            'telefono' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $autoVerify = (bool) config('linea61.auto_verify_email');

        $user = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'ci' => $request->ci,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($autoVerify) {
            $user->markEmailAsVerified();
        }

        if (! $user->hasVerifiedEmail()) {
            event(new Registered($user));
        }

        Auth::login($user);

        // Sin verificación por correo el usuario pasa directo a la pantalla
        // de rol pendiente; el enlace firmado se conserva como respaldo.
        return $autoVerify
            ? redirect()->route('pending.role')
            : redirect()->route('verification.notice');
    }
}
