<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{

/**
* @OA\GET(
*     path="/api/getRoles",
*     summary="Obtener los roles",
*     tags={"Roles"},
* @OA\Response(
*         response=422,
*         description="Error al obtener los Roles"
*     ),
* @OA\Response(
*         response="200",
*         description="Exito al obtener los roles"
*     ),
* )
*/

    public function getRoles(){
        try {

            return Role::all();
        
        }catch(Exception $e){

            return response()->json([
                'success'  => false,
                'message'  => 'Error al solicitar los roles'
            ],422);
        }
        
    }

/**
* @OA\GET(
*     path="/api/addRole",
*     summary="Agregar Roles",
*     tags={"Roles"},
*  @OA\Parameter(
*      name="type",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
* @OA\Response(
*         response=422,
*         description="Error al agregar un rol"
*     ),
* @OA\Response(
*         response="200",
*         description="Exito al agregar un rol"
*     ),
*     security={
*       {"bearerAuth": {}}
*     }
* )
*/
    public function addRole (Request $request) {

    
        try{
            
            $role = new Role();
            $role->fill($request->all());
            $role->save();
    
            return [
                'success'   => true,
                'message'   => "Rol Agregado",
            ];
    
        } catch ( Exception $e ){
            
            return [
                'success'   => false,
                'message'   => "Error al agregar el rol",
            ];
        }
    }
}

