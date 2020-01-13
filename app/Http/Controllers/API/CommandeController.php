<?php

namespace App\Http\Controllers\API;

use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\CommandeHasProduit as CHP;

use Validator;


class CommandeController extends BaseController
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
                $cmds = Commande::with("cmh","cmh.produit")->get();
    
                return $this->sendResponse($cmds, "Fetched Commandes successfully");
            }
            else if($user->hasRole("gestionnaire")) {
                $sid = $user->getSupermarcheId();
                if(!$sid) {
                    return $this->sendError("gestionnaire.supermarche.not_found", 403); 
                }
                $produits_id = Produit::where("supermarche_id",$sid)->pluck("id");
                $cmhs = CHP::with("produit")->whereIn("produit_id",$produits_id)->get();

                return $this->sendResponse($cmhs,"Fetched commandes succesfully");
            }
            else if($user->hasRole("user")) {
                $cmds = Commande::with("cmh","cmh.produit")->where("user_id",$user->id);

                return $this->sendResponse($cmds,"Fetched commandes successfully");
            }
            else {
                return $this->sendError("user.unauthorized",403);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("add commande")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "items" => "required|array",
            "items.*.produit_id" => "required|numeric|exists:produits,id",
            "items.*.produit_qtt" => "required|numeric|min:1",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }
        
        $commande = new Commande();
        $commande->user_id = $user->id;
        $commande->save();
        $cost = 0;
        
        foreach($request->items as $item) {
            $pr = Produit::find($item["produit_id"]);
            if(!$pr) continue;
            $cp = new CHP();
            $cp->commande_id = $commande->id;
            $cp->produit_id  = $item["produit_id"];
            $cp->qtt         = $item["produit_qtt"];
            $cp->save();
            $cost += $pr->cost * $item["produit_qtt"];
        }

        $commande->cost = $cost;
        $commande->save();

        return $this->sendResponse($commande,"Commande created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\commande  $commande
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->getAuthenticatedUser();
        
        if(!$user) {
            return $this->sendError("user.unauthorized", 403);
        }
        else {
            if($user->hasRole("user") || $user->hasRole("administrator")) {
                $cmds = Commande::with("cmh","cmh.produit")->where("id",$id)->get();
    
                return $this->sendResponse($cmds, "Fetched Commandes successfully");
            }
            else {
                return $this->sendError("user.unauthorized",403);
            }
        }
    }

    public function validateReception(Request $request,$id) {
        $user = $this->getAuthenticatedUser();

        $cmd = Commande::find($id);

        if(!$cmd) {
            return $this->sendError("commande.not_found",404);
        }

        if(!$user || !$user->can("validate reception commande") || $cmd->user_id != $user->id) {
            return $this->sendError("user.unauthorized",403);
        }

        $cmd->received = true;
        $cmd->save();

        return $this->sendResponse($cmd, "Validated successfully");
    }

    public function evalCommande(Request $request,$id) {
        $user = $this->getAuthenticatedUser();

        $cmd = Commande::find($id);

        if(!$cmd) {
            return $this->sendError("commande.not_found",404);
        }
        
        if(!$user || !$user->can("validate reception commande") || $cmd->user_id != $user->id) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "evaluation" => "required|numeric|min:0|max:5"
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }
        
        $cmd->evaluation = $request->evaluation;
        $cmd->save();

        return $this->sendResponse($cmd, "Validated successfully");
    }
}
