<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Deconnecte un utilisateur dont le compte a ete suspendu (status != 1).
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->status != 1) {
            // API mobile : le jeton utilise est revoque
            if (!$request->hasSession()) {
                $user->currentAccessToken()?->delete();

                return response()->json(['message' => "Votre compte a été suspendu."], 403);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => "Votre compte a été suspendu."]);
        }

        return $next($request);
    }
}
