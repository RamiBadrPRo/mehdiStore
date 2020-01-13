<?php

namespace App\Http\Controllers\API;

use App\Models\Supermarche;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Validator;
use Storage;

class SupermarcheController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view supermarche")) {
            return $this->sendError("user.unauthorized",403);
        }

        $data = Supermarche::all();

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
        
        if(!$user || !$user->can("add supermarche")) {
            return $this->sendError("user.unauthorized",403);
        }
        
        $sanitize = Validator::make($request->all(),[
            "nom" => "required|string",
            "adresse" => "required|string",
            "image_path" => "required|string",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        if(!Storage::disk("public")->exists($request->image_path)) {
            return $this->sendError("supermarche.avatar_url.not_found",403);
        }

        $input = $request->all();
        $data = Supermarche::create($input);

        return $this->sendResponse($data,"Supermarche created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\supermarche  $supermarche
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view supermarche")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sup = Supermarche::find($id);
        if(!$sup) {
            return $this->sendError("supermarche.not_found",404);
        }

        return $this->sendResponse($sup,"Supermarche fetched successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\supermarche  $supermarche
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        $user = $this->getAuthenticatedUser();
        if(!$user || !$user->can("edit supermarche")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "nom" => "required|string",
            "adresse" => "required|string",
            "image_path" => "required|string",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }
        
        $supermarche = Supermarche::find($id);
        if(!$supermarche) {
            return $this->sendError("supermarche.not_found",404);
        }

        if(!Storage::disk("public")->exists($request->image_path)) {
            return $this->sendError("supermarche.avatar_url.not_found",403);
        }

        $supermarche->nom = $request->nom;
        $supermarche->adresse = $request->adresse;
        $supermarche->image_path = $request->image_path;
        $supermarche->save();
       
        return $this->sendResponse(true,"Updated supermarche successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\supermarche  $supermarche
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("delete supermarche")) {
            return $this->sendError("user.unauthorized",403);
        }

        Supermarche::where("id",$id)->delete();

        return $this->sendResponse(true, "Removed supermarche successfully");
    }
}
