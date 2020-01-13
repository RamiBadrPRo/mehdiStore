<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = [
        "user_id", "livreur_id", "received", "evaluation", "cost"
    ];

    public function client() {
        $this->hasOne("App\User","client_id","id");
    }

    public function cmh() {
        return $this->hasMany("App\Models\CommandeHasProduit","commande_id","id");
    }
}
