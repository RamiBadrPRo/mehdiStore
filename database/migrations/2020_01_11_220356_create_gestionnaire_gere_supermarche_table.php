<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGestionnaireGereSupermarcheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestionnaire_gere_supermarche', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("user_id");
            $table->integer("supermarche_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gestionnaire_gere_supermarche');
    }
}
