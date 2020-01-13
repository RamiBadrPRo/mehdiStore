<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Produit;
use App\Models\GestionnaireGereSupermarche as GGS;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gereProduit($produit_id) {
        $produit = Produit::find($produit_id);
        if(!$produit) return false;

        $g = GGS::where("supermarche_id",$produit->supermarche_id)->first();
        if(!$g) return false;

        return $g->user_id == $this->id;
    }

    public function getSupermarcheId() {
        $g = GGS::where("user_id", $this->id)->first();
        if(!$g) return null;

        return $g->supermarche_id;
    }
}
