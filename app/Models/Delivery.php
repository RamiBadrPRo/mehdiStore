<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{

    protected $fillable = [
        "commande_id", "livreur_id", "delivered"
    ];

    public function commande() {
        return $this->belongsTo("App\Models\Commande","commande_id","id");
    }

    public function livreur() {
        return $this->belongsTo("App\User","livreur_id","id");
    }
}
