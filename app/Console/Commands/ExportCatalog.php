<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/*
 * Copie des jeux et des cartes integree a l'application Android (android/app/src/main/assets),
 * pour qu'elle soit jouable des le premier lancement, meme sans connexion.
 * Le contenu vient de l'API (/api/v1), au meme format que le catalogue telecharge par l'application.
 * A relancer avant de compiler une nouvelle version de l'application.
 */
class ExportCatalog extends Command
{
    protected $signature = 'catalog:export {lang=fr} {--path= : Fichier de sortie (par defaut : assets de l\'application Android)}';

    protected $description = "Exporte les cartes pour l'application Android (jeu sans connexion des le premier lancement)";

    public function handle()
    {
        $lang = $this->argument('lang');
        $path = $this->option('path') ?: base_path("android/app/src/main/assets/catalog_{$lang}.json");

        $catalog = [
            'lang' => $lang,
            'version' => $this->api('cards/version', $lang)['version'],
            'games' => $this->api('games', $lang)['data'],
            'cards' => $this->api('cards', $lang)['data'],
            'werewolfCards' => $this->api('werewolf/cards', $lang)['data'],
        ];

        file_put_contents($path, json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->info(sprintf(
            '%d jeux, %d cartes, %d roles du Loup-Garou -> %s (version %s)',
            count($catalog['games']), count($catalog['cards']), count($catalog['werewolfCards']), $path, $catalog['version']
        ));

        return self::SUCCESS;
    }

    /** Appel interne a l'API, sans passer par le reseau */
    private function api(string $uri, string $lang): array
    {
        $response = app(Kernel::class)->handle(Request::create('/api/v1/'.$uri, 'GET', ['lang' => $lang]));
        if (!$response->isSuccessful()) {
            throw new \RuntimeException("API /api/v1/$uri : HTTP ".$response->getStatusCode());
        }

        return json_decode($response->getContent(), true);
    }
}
