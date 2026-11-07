<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conductor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (! Auth::validate([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        $this->verificarCorreoAutomaticamente($user);
        $this->vincularPerfilConductor($user);

        $user->load(['conductor', 'roles']);
        $token = $user->createToken($credentials['device_name'] ?? 'api-token');

        return response()->json([
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        $this->verificarCorreoAutomaticamente($user);
        $this->vincularPerfilConductor($user);

        return response()->json($user->load(['conductor', 'roles']));
    }

    /**
     * Marca la cuenta como verificada cuando la instalación no exige el
     * enlace de correo. Cubre tanto el login como los tokens ya emitidos
     * (la app consulta /me al abrirse).
     */
    private function verificarCorreoAutomaticamente(User $user): void
    {
        if (! config('linea61.auto_verify_on_login') || $user->hasVerifiedEmail()) {
            return;
        }

        $user->markEmailAsVerified();
        $user->refresh();
    }

    /**
     * Enlaza automáticamente una cuenta de rol conductor con su ficha
     * operativa cuando todavía no tiene user_id. El enlace se hace por
     * correo o por CI para no depender de una asignación manual.
     */
    private function vincularPerfilConductor(User $user): void
    {
        if ($user->conductor()->exists()) {
            return;
        }

        if (! $user->hasAnyRole(['conductor', 'admin', 'fiscalizador'])) {
            return;
        }

        $conductor = Conductor::query()
            ->whereNull('user_id')
            ->where(function ($query) use ($user) {
                $query->where('correo', $user->email);

                if (! empty($user->ci)) {
                    $query->orWhere('ci', $user->ci);
                }
            })
            ->first();

        if ($conductor) {
            $conductor->update(['user_id' => $user->id]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
