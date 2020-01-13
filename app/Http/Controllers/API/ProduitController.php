<?php

namespace App\Http\Controllers\API;

use App\Models\Produit;
use App\Models\GestionnaireGereSupermarche as GGS;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;


use Validator;
use Storage;

class ProduitController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view produit")) {
            return $this->sendError("user.unauthorized",403);
        }

        $g = GGS::where("user_id",$user->id)->first();
        if(!$g) {
            return $this->sendError("gestionnaire.without_supermarche");
        }

        $data = Produit::where("supermarche_id",$g->supermarche_id)->get();

        return $this->sendResponse($data,"Fetched Data successfully");
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
        
        if(!$user || !$user->can("add produit")) {
            return $this->sendError("user.unauthorized",403);
        }
        
        $sanitize = Validator::make($request->all(),[
            "nom" => "required|string",
            "description" => "required|string",
            "rubrique_id"    => "required|numeric|exists:rubriques,id",
            "photo_path"     => "required|string",
            "cost"           => "required|numeric",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        if(!Storage::disk("public")->exists($request->photo_path)) {
            return $this->sendError("produit.avatar_url.not_found",403);
        }

        $input = $request->all();
        $input["supermarche_id"] = $user->getSupermarcheId();
        $prod = Produit::create($input);

        return $this->sendResponse($prod,"Produit created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\produit  $produit
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->getAuthenticatedUser();
        
        if(!$user || !$user->can("view produit")) {
            return $this->sendError("user.unauthorized",403);
        }

        $prod = Produit::find($id);
        if(!$prod || !$user->gereProduit($id)) {
            return $this->sendError("produit.not_found",403);
        }        

        return $this->sendResponse($prod,"Produit fetched successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\produit  $produit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser();
        
        if(!$user || !$user->can("add produit") || !$user->gereProduit($id)) {
            return $this->sendError("user.unauthorized",403);
        }
        
        $sanitize = Validator::make($request->all(),[
            "nom" => "required|string",
            "description" => "required|string",
            "rubrique_id"    => "required|numeric|exists:rubriques,id",
            "photo_path"     => "required|string",
            "cost"           => "required|numeric",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        if(!Storage::disk("public")->exists($request->photo_path)) {
            return $this->sendError("produit.avatar_url.not_found",403);
        }

        $prod = Produit::find($id);
        $input = $request->all();
        $input["supermarche_id"] = $user->getSupermarcheId();

        if(!$prod) {
            return $this->sendError("produit.not_found",403);
        } 

        $prod->fill($input)->save();

        return $this->sendResponse($prod,"Produit created successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\produit  $produit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("delete produit") || !$user->gereProduit($id)) {
            return $this->sendError("user.unauthorized",403);
        }

        Produit::where("id",$id)->delete();

        return $this->sendResponse(true, "Removed supermarche successfully");
    }


    /**
     * Mettre en place une promotion
     *
     * @param  \App\produit  $produit
     * @return \Illuminate\Http\Response
     */
    public function setPromotion(Request $request) {
        $user = $this->getAuthenticatedUser();
        if(!$user || !$user->can("add produit") || !$user->gereProduit($request->produit_id)) {
            return $this->sendError("user.unauthorized",403);
        }
        
        $sanitize = Validator::make($request->all(),[
            "produit_id" => "required|numeric",
            "promotion" => "required|numeric|min:1|max:100"
        ]);


        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        $prod = Produit::find($request->produit_id);
        $prod->promotion = $request->promotion;
        $prod->save();

        return $this->sendResponse($prod,"Promotion mise en place avec success");
    }
}
