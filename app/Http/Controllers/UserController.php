<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\User\registerRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

/**
* @OA\POST(
*     path="/api/register",
*     summary="Registrar usuarios",
* @OA\RequestBody(
*    required=true,
*    description="Introducir Datos",
*    @OA\JsonContent(
*       required={"email","name","password"},
*       @OA\Property(property="name", type="string",format="name",example="Nombre"),
*       @OA\Property(property="email", type="string", format="email", example="ejemplo@mail.com"),
*       @OA\Property(property="password", type="string", format="password", example="Contraseña") 
*    ),
* ),
* @OA\Response(
*         response=200,
*         description="Usuario Registrado"
*     ),
* @OA\Response(
*         response="default",
*         description="Ha ocurrido un error."
*     )
* )
*/

    public function register (registerRequest $request) {
        
        try{
            
            $register = new User();
            $register->fill($request->all());
            $pwd = hash('sha256',$register->password);
            $register->password = $pwd;
            $register->save();

            return [
                'success'   => true,
                'message'   => "Registro Exitoso",
            ];

        } catch ( Exception $e ){
            
            return [
                'success'   => false,
                'message'   => "Registro Fallido",
            ];
        }
     }
}
