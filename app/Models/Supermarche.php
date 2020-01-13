<?php

namespace App\Models;

use App\Models\GestionnaireGereSupermarche;
use Illuminate\Database\Eloquent\Model;

class supermarche extends Model
{

    protected $fillable = [
        "nom", "adresse", "image_path"
    ];
    
    public static function managedBy($id) {
        $pv = GestionnaireGereSupermarche::where("user_id",$id)->first();
        if(!$pv) return null;

        return self::where("id",$pv->supermarche_id)->first();
    }
}
