<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\LivreurDisponible as LD;
use App\User;
use Validator;
use Hash;
use Storage;

class LivreurController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view livreur")) {
            return $this->sendError("user.unauthorized",403);
        }

        $data = User::role("livreur")->get();

        return $this->sendResponse($data,"Fetched All livreurs successfully");
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
        
        if(!$user || !$user->can("add livreur")) {
            return $this->sendError("user.unauthorized",403);
        }
        
        $sanitize = Validator::make($request->all(),[
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|string",
            "avatar_path" => "required|string",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        if(!Storage::disk("public")->exists($request->avatar_path)) {
            return $this->sendError("gestionnaire.avatar_url.not_found",403);
        }

        $input = $request->all();
        $input["password"] = Hash::make($input["password"]);
        $gest = User::create($input);
        $gest->assignRole("livreur");

        LD::create([
            "user_id" => $gest->id,
            "disponible" => true
        ]);

        return $this->sendResponse($gest,"Supermarche created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view livreur")) {
            return $this->sendError("user.unauthorized",403);
        }

        $gest = User::role("livreur")->with("roles")->where("id",$id)->first();
        if(!$gest) {
            return $this->sendError("livreur.not_found",404);
        }

        return $this->sendResponse($gest,"Livreur fetched successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser();
        if(!$user || !$user->can("edit livreur")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "name" => "string",
            "email" => "email",
            "password" => "string",
            "avatar_path" => "string"
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        $upd = User::role("livreur")->where("id",$id)->first();
        if(!$upd) return $this->sendError("livreur.not_found",404);
        $upd->name = $request->name;

        if($request->has("email") && $upd->email != $request->email) {
            $ant = User::where("email",$request->email)->first();
            if(!$ant) {
                $upd->email = $request->email;
            }
            else {
                return $this->sendError("Email Already taken",402);
            }
        }

        if($request->has("password")) {
            $upd->password = Hash::make($request->password);
        }

        if($request->has("avatar_path")) {
            if(Storage::disk("public")->exists($request->avatar_path)) {
                $upd->avatar_path = $request->avatar_path;
            }
            else {
                return $this->sendError("gestionnaire.avatar.not_found",404);
            }
        }

        $upd->save();
       
        return $this->sendResponse(true,"Updated livreur successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("delete livreur")) {
            return $this->sendError("user.unauthorized",403);
        }

        User::role("livreur")->where("id",$id)->delete();

        return $this->sendResponse(true, "Removed livreur successfully");
    }

    /**
     * Set Status Of Livreur
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

     public function setStatus(Request $request) {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("edit status")) {
            return $this->sendError("user.unauythorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "status" => "required|numeric",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        LD::where("user_id",$user->id)->update(["disponible" => boolval($request->status)]);

        return $this->sendResponse(true,"Updated Status Successfully");
    }
}
