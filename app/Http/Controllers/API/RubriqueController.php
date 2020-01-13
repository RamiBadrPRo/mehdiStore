<?php

namespace App\Http\Controllers\API;

use App\Models\Rubrique;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use Validator;

class RubriqueController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view rubrique")) {
            return $this->sendError("user.unauthorized",403);
        }

        $data = Rubrique::all();

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
        
        if(!$user || !$user->can("add rubrique")) {
            return $this->sendError("user.unauthorized",403);
        }
        
        $sanitize = Validator::make($request->all(),[
            "nom" => "required|string",
            "description" => "required|string"
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }

        $input = $request->all();
        $data = Rubrique::create($input);

        return $this->sendResponse($data,"Rubrique created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\rubrique  $rubrique
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("view rubrique")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sup = Rubrique::find($id);
        if(!$sup) {
            return $this->sendError("rubrique.not_found",404);
        }

        return $this->sendResponse($sup,"Rubrique fetched successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\rubrique  $rubrique
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser();
        if(!$user || !$user->can("edit rubrique")) {
            return $this->sendError("user.unauthorized",403);
        }

        $sanitize = Validator::make($request->all(),[
            "nom" => "required|string",
            "description" => "required|string"
        ]);

        if($sanitize->fails()) {
            $errors = $sanitize->errors();
            return $this->sendError("Failed to sanitize",405,$errors->all());
        }
        
        $rub = Rubrique::find($id);
        if(!$rub) {
            return $this->sendError("rubrique.not_found",404);
        }

        $rub->nom = $request->nom;
        $rub->description = $request->description;
        $rub->save();
       
        return $this->sendResponse(true,"Updated supermarche successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\rubrique  $rubrique
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->getAuthenticatedUser();

        if(!$user || !$user->can("delete rubrique")) {
            return $this->sendError("user.unauthorized",403);
        }

        Rubrique::where("id",$id)->delete();

        return $this->sendResponse(true, "Removed rubrique successfully");
    }
}
