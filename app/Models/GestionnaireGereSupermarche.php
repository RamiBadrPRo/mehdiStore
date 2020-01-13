<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GestionnaireGereSupermarche extends Pivot
{

    protected $fillable = [
        "user_id", "supermarche_id"
    ];

    public function gestionnaire() {
        $this->hasOne("App\User","user_id","id");
    }

    public function supermarche() {
        $this->hasOne("App\Models\Supermarche","supermarche_id","id");
    }
}
