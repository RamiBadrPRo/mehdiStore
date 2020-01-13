<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Delivery;
use App\Models\Commande;
use App\User;

use Validator;

class DeliveryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();
        
        if(!$user) {
            return $this->sendError("user.unauthorized", 403);
        }
        else {
            if($user->hasRole("administrator")) {
                $del = Delivery::with("commande","livreur","commande.cmh","commande.cmh.produit")->get();

                return $this->sendResponse($del,"Fetched All Deliveries successfully");
            }
            else if($user->hasRole("livreur")) {
                $del = Delivery::with("commande","commande.cmh","commande.cmh.produit")->where("livreur_id",$user->id)->get();

                return $this->sendResponse($del,"Fetched All Deliveries successfully");
            }
            else {
                return $this->sendError("user.unauthorized",403);
            }
        }
    }

    /**
     * Setup new delivery
     * 
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("add delivery")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "commande_id" => "required|numeric|exists:commandes,id",
            "livreur_id"  => "required|numeric|exists:users,id"
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        $livreur = User::find($request->livreur_id);
        if(!$livreur->hasRole("livreur") ) {
            return $this->sendError("user.not_livreur",403);
        }

        if(!$livreur->disponible()) {
            return $this->sendError("livreur.not_disponible",403);
        }

        $commande = Commande::find($request->commande_id);
        if($commande->livreur_id != null) {
            return $this->sendError("commande.has_livreur_already",403);
        }

        $commande->livreur_id = $livreur->id;
        $commande->save();

        $input = $request->all(); 
        $del = Delivery::create($input);

        return $this->sendResponse($del,"Delivery created succesfully");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->getAuthenticatedUser();
        
        if(!$user) {
            return $this->sendError("user.unauthorized", 403);
        }
        else {
            if($user->hasRole("administrator")) {
                $del = Delivery::with("commande","livreur","commande.cmh","commande.cmh.produit")->where("id",$id)->get();

                return $this->sendResponse($del,"Fetched All Deliveries successfully");
            }
            else if($user->hasRole("livreur")) {
                $del = Delivery::with("commande","commande.cmh","commande.cmh.produit")->where("id",$id)->where("livreur_id",$user->id)->get();

                return $this->sendResponse($del,"Fetched All Deliveries successfully");
            }
            else {
                return $this->sendError("user.unauthorized",403);
            }
        }
    }

    public function validateDelivered($id) {
        $user = $this->getAuthenticatedUser();

        $cmd = Delivery::find($id);

        if(!$cmd) {
            return $this->sendError("commande.not_found",404);
        }

        if(!$user || !$user->can("validate delivery commande") || $cmd->livreur_id != $user->id) {
            return $this->sendError("user.unauthorized",403);
        }

        $cmd->delivered = true;
        $cmd->save();

        return $this->sendResponse($cmd, "Validated successfully");
    }
}
