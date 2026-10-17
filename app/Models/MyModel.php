<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MyModel extends Model
{
    public static function boot()
    {
        parent::boot();

        // L'auteur n'est renseigne qu'a la creation (sinon chaque modification l'ecrase)
        static::creating(function ($table) {
            if (Auth::user() != null) {
                $table->created_by = Auth::user()->name;
            }
        });

        static::updating(function ($table) {
            if (Auth::user() != null) {
                $table->updated_by = Auth::user()->name;
            }
        });

        static::deleting(function ($table) {
            if (Auth::user() != null) {
                $table->deleted_by = Auth::user()->name;
            }
        });
    }
}
