<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Card extends MyModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gam_cards';
    public $timestamps = true;

    // Nombre de mots saisis par jeu : Brainstorm, Taboo, Loup-Garou (extension).
    // Meme valeur que CARD_WORDS_BY_GAME dans public/js/utils.js
    public const WORDS_BY_GAME = [2 => 10, 3 => 5, 4 => 1];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($card) {
            if (Auth::check()) {
                $card->user_id = Auth::id();
            }
        });
    }

    public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
