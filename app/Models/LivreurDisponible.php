<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LivreurDisponible extends Pivot
{
    protected $fillable = [
        "user_id", "disponible"
    ];
    
    public function livreur() {
        return $this->hasOne("App\User","user_id","id");
    }
}
