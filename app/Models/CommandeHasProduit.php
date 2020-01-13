<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CommandeHasProduit extends Pivot
{
    public function commande() {
        return $this->hasOne("App\Models\Commande","commande_id","id");
    }

    public function produit() {
        return $this->belongsTo("App\Models\Produit","produit_id","id");
    }
}
