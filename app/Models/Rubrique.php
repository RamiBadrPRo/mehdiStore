<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rubrique extends Model
{
    protected $fillable = [
        "nom", "description"
    ];
}
