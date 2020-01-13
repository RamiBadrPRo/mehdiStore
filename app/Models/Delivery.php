<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public function commande() {
        return $this->hasOne("App\Models\Commande","commande_id","id");
    }

    public function livreur() {
        return $this->hasOne("App\User","livreur_id","id");
    }
}
