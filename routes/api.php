<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  
Route::any('register', 'API\AuthAPIController@register')->name("register");
Route::any('login', 'API\AuthAPIController@login')->name("login");
Route::middleware('auth:api')->group( function () {
    Route::resource("Supermarche",'API\SupermarcheController');

    Route::get("/Gestionnaire","API\GestionnaireController@index");
    Route::post("/Gestionnaire","API\GestionnaireController@store");
    Route::post("/Gestionnaire/Assign","API\GestionnaireController@assignToSup");
    Route::get("/Gestionnaire/{id}","API\GestionnaireController@show");
    Route::post("/Gestionnaire/{id}","API\GestionnaireController@update");
    Route::delete("/Gestionnaire/{id}","API\GestionnaireController@destroy");

    Route::get("/Livreur","API\LivreurController@index");
    Route::post("/Livreur","API\LivreurController@store");
    Route::post("/Livreur/setStatus","API\LivreurController@setStatus");
    Route::get("/Livreur/{id}","API\LivreurController@show");
    Route::post("/Livreur/{id}","API\LivreurController@update");
    Route::delete("/Livreur/{id}","API\LivreurController@destroy");

    Route::get("/Rubrique","API\RubriqueController@index");
    Route::post("/Rubrique","API\RubriqueController@store");
    Route::get("/Rubrique/{id}","API\RubriqueController@show");
    Route::post("/Rubrique/{id}","API\RubriqueController@update");
    Route::delete("/Rubrique/{id}","API\RubriqueController@destroy");

    Route::get("/Produit","API\ProduitController@index");
    Route::post("/Produit","API\ProduitController@store");
    Route::post("/Produit/UploadImg","API\ProduitController@uploadImg");
    Route::post("/Produit/SetPromotion","API\ProduitController@setPromotion");
    Route::get("/Produit/{id}","API\ProduitController@show");
    Route::post("/Produit/{id}","API\ProduitController@update");
    Route::delete("/Produit/{id}","API\ProduitController@destroy");

    Route::post("/Commande","API\CommandeController@store");
    Route::get("/Commande","API\CommandeController@index");
    Route::get("/Commande/{id}","API\CommandeController@show");
    Route::post("/Commande/ValidateReceived/{id}","API\CommandeController@validateReception");
    Route::post("/Commande/Eval/{id}","API\CommandeController@evalCommande");

    Route::get("/Delivery","API\DeliveryController@index");
    Route::post("/Delivery","API\DeliveryController@store");
    Route::get("/Delivery/{id}","API\DeliveryController@show");
    Route::post("/Delivery/ValidateDelivery/{id}","API\DeliveryController@validateDelivered");


    Route::get("/getAuthenticatedUser","API\AuthAPIController@getAuthUser");
});
?>