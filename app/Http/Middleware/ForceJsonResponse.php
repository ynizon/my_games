<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * L'API repond toujours en JSON (erreurs de validation, 401, 404...),
 * meme si l'application mobile n'envoie pas "Accept: application/json".
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
