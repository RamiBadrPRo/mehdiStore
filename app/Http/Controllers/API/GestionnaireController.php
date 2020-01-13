<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\GestionnaireGereSupermarche as GGS;
use App\User;
use Validator;
use Hash;

class GestionnaireController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view gestionnaire")) {
            return $this->sendError("user.unauthorized",403);
        }

        $data = User::role("gestionnaire")->get();

        return $this->sendResponse($data,"Fetched All gestionnaire successfully");
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
        
        if(!$user || !$user->can("add gestionnaire")) {
            return $this->sendError("user.unauthorized",403);
        }
        
        $sanitize = Validator::make($request->all(),[
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|string",
            "avatar" => "required|file|mimes:jpeg,jpg,png,gif|max:50000"
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        $input = $request->all();
        $input["password"] = Hash::make($input["password"]);
        if($request->hasFile("avatar")) {
            $path = $request->file("avatar")->store("public/UserProfiles");
            unset($input["avatar"]);
            $input["avatar_path"] = substr($path,7);
        }
        
        $gest = User::create($input);
        $gest->assignRole("Gestionnaire");

        return $this->sendResponse($gest,"Gestionnaire created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view gestionnaire")) {
            return $this->sendError("user.unauthorized",403);
        }

        $gest = User::role("gestionnaire")->with("roles")->where("id",$id)->first();
        if(!$gest) {
            return $this->sendError("gestionnaire.not_found",404);
        }

        return $this->sendResponse($gest,"Gestionnaire fetched successfully");
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
        if(!$user || !$user->can("edit gestionnaire")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "name" => "string",
            "email" => "email",
            "password" => "string",
            "avatar" => "file|mimes:jpeg,jpg,png,gif|max:50000"
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        $upd = User::role("gestionnaire")->where("id",$id)->first();
        if(!$upd) return $this->sendError("gestionnaire.not_found",404);
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

        if($request->hasFile("avatar")) {
            $path = $request->file("avatar")->store("public/UserProfiles");
            $upd->avatar_path = substr($path,7);
        }

        $upd->save();
       
        return $this->sendResponse(true,"Updated supermarche successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("delete gestionnaire")) {
            return $this->sendError("user.unauthorized",403);
        }

        User::role("gestionnaire")->where("id",$id)->delete();

        return $this->sendResponse(true, "Removed supermarche successfully");
    }

    /**
     * Assign gestionnaire to supermarche
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

     public function assignToSup(Request $request) {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("edit gestionnaire")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "user_id" => "required|numeric|exists:users,id",
            "supermarche_id" => "required|numeric|exists:supermarches,id",
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        $gu = User::role("gestionnaire")->where("id",$request->user_id)->first();
        if(!$gu) return $this->sendError("User is not gestionnaire",403);

        GGS::where("user_id",$request->user_id)->delete();
        GGS::where("supermarche_id",$request->supermarche_id)->delete();
        $gestion = GGS::create($request->all());

        return $this->sendResponse($gestion,"User Assigned successfully");
     }

}