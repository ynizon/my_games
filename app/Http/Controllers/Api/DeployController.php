<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Appele par la CI apres l'envoi FTP (pas d'acces SSH sur l'hebergement) :
 * migrations et vidage des caches. Desactive tant que DEPLOY_TOKEN n'est pas defini dans le .env.
 */
class DeployController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = (string) config('app.deploy_token');
        if ($token === '' || !hash_equals($token, (string) $request->header('X-Deploy-Token'))) {
            abort(404);
        }

        $output = '';
        foreach (['migrate' => ['--force' => true], 'optimize:clear' => []] as $command => $options) {
            Artisan::call($command, $options);
            $output .= Artisan::output();
        }

        return response()->json(['status' => 'ok', 'output' => $output]);
    }
}
