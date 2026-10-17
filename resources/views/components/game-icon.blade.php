@props(['name'])
{{-- Icone d'un jeu sur la couleur du jeu (voir theme.css : .tile-<adresse du jeu>) --}}
<span {{ $attributes->merge(['class' => 'game-icon tile-'.Helper::gameSlug($name)]) }} aria-hidden="true">{{ Helper::gameIcon($name) }}</span>
