<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        "nom", "description", "supermarche_id", "rubrique_id", "photo_path", "cost", "promotion"
    ];

    public function supermarche() {
        $this->hasOne("App\Models\Supermarche","supermarche_id","id");
    }
}
