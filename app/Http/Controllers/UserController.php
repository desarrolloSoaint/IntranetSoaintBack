<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\registerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Validator;

class UserController extends Controller
{


/**
* @OA\POST(
*     path="/api/register",
*     summary="Registrar usuarios",
*     tags={"Security"},
* @OA\RequestBody(
*    required=true,
*    description="Introducir Datos",
*    @OA\JsonContent(
*       required={"email","password","role_id"},
*       @OA\Property(property="email", type="string", format="email", example="ejemplo@mail.com"),
*       @OA\Property(property="password", type="string", format="password", example="Contraseña"),
*       @OA\Property(property="role_id", type="integer", format="integer", example="1") 
*    ),
* ),
* @OA\Response(
*         response=200,
*         description="Usuario Registrado"
*     ),
* @OA\Response(
*         response="422",
*         description="Error de Validacion."
*     )
* )
*/
    public function register (registerRequest $request) {        
    
        try{
            
            $register = new User();
            $register->fill($request->all());
            $pwd = bcrypt($register->password);
            $register->password = $pwd;
            $register->save();

            return $this->login($request);

        } catch ( Exception $e ){
            
            return [
                'success'   => false,
                'message'   => "Registro Fallido",
            ];
        }
     }


    public function login(Request $request){

/**
* @OA\POST(
*     path="/api/login",
*     summary="Login",
*     tags={"Security"},
* @OA\RequestBody(
*    required=true,
*    description="Introducir Datos",
*    @OA\JsonContent(
*       required={"email","password"},
*       @OA\Property(property="email", type="string", format="email", example="ejemplo@mail.com"),
*       @OA\Property(property="password", type="string", format="password", example="Contraseña") 
*    ),
* ),
* @OA\Response(
*         response=422,
*         description="Error de Validacion"
*     ),
* @OA\Response(
*         response="200",
*         description="Login Exitoso."
*     ),
* @OA\Response(
*         response="401",
*         description="Error de Credenciales."
*     )
* )
*/

        $credentials = $request->only('email','password');
        $validator = Validator::make($credentials,[
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'success'  => false,
                'message'  => 'Ingrese un correo o contraseña valida'
            ],422);
        }

        $token = JWTAuth::attempt($credentials);

        if ($token){
            return response()->json([
                'success'  => true,
                'access_token'  => $token,
                'user' => User::where('email',$credentials['email'])->get()->first()
            ],200);
        }else{
            return response()->json([
                'success'  => false,
                'message'  => 'Correo o Contraseña incorrecta'
            ],401);
        }

    }

/**
* @OA\POST(
*     path="/api/refreshToken",
*     summary="Refresh Token",
*     tags={"Token"},
* @OA\Response(
*         response=422,
*         description="Error al refrescar el token"
*     ),
* @OA\Response(
*         response="200",
*         description="Refresh Token Exitoso"
*     ),
*     security={
*       {"bearerAuth": {}}
*     }
* )
*/

    public function refreshToken(){
        $token = JWTAuth::getToken();  
        
        try{

            $token = JWTAuth::refresh($token);

            return response()->json([
                'success'  => true,
                'message'  => $token
            ],200);

        } catch (TokenBlackListedException $e){

            return response()->json([
                'success'  => false,
                'message'  => 'Necesitas iniciar sesion otra vez'
            ],422);

        };
    }

/**
* @OA\POST(
*     path="/api/logout",
*     summary="Logout",
*     tags={"Security"},
* @OA\Response(
*         response=422,
*         description="Error al cerrar sesion"
*     ),
* @OA\Response(
*         response="200",
*         description="Logout Exitoso."
*     ),
*     security={
*       {"bearerAuth": {}}
*     }
* )
*/
    public function logout() {
        $token = JWTAuth::getToken();

        try{
            JWTAuth::invalidate($token);
            return response()->json([
                'success'  => true,
                'message'  => 'Cerro Sesion'
            ],200);
        }catch(JWTException $e){
            return response()->json([
                'success'  => false,
                'message'  => 'Error al cerrar sesion'
            ],422);
        }
    }

    

/**
* @OA\POST(
*     path="/api/getUserRole",
*     summary="Obtener Rol del Usuario",
*     tags={"User"},
* @OA\RequestBody(
*    required=true,
*    description="Introducir Datos",
*    @OA\JsonContent(
*       required={"email"},
*       @OA\Property(property="email", type="string", format="email", example="ejemplo@mail.com") 
*    ),
* ),
* @OA\Response(
*         response=200,
*         description="Exito"
*     ),
* @OA\Response(
*         response="422",
*         description="Error"
*     ),
*     security={
*       {"bearerAuth": {}}
*     }
* )
*/
    public function getUserRole (Request $request) {            
        try{
            
            $user = User::where('email',$request['email'])->get()->first();

            return $user->role;

        } catch ( Exception $e ){
            
            return [
                'success'   => false,
                'message'   => "Error al obtener el rol",
            ];
        }
    }

}
