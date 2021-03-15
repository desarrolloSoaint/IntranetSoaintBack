<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rule;
use App\Http\Requests\Rule\ruleRequest;
use Validator;

class RuleController extends Controller
{

/**
* @OA\GET(
*     path="/api/getRules",
*     operationId="getRules",
*     summary="Obtener reglas",
*     tags={"Rules"},
* @OA\Response(
*          response=200,
*          description="Successful operation",
*          @OA\MediaType(
*           mediaType="application/json",
*          )
*      ),
* @OA\Response(
*         response=401,
*         description="Unauthenticated",
*      ),
* @OA\Response(
*         response=403,
*         description="Forbidden"
*      ),
* @OA\Response(
*         response=400,
*         description="Bad Request"
*      ),
* @OA\Response(
*          response=404,
*          description="Not found"
*   ),
* security={
*       {"bearerAuth": {}}
*     }
* )
*/

    public function index()
    {
        try{
            
            return Rule::orderBy('id', 'ASC')->get();
        
        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al obtener las reglas",
            ];
        }
    }

/**
* @OA\POST(
*     path="/api/addRules",
*     operationId="addRules",
*     summary="Registrar regla",
*     tags={"Rules"},
*  @OA\Parameter(
*      name="name",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="start_time",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="finish_time",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
* @OA\Response(
*          response=200,
*          description="Successful operation",
*          @OA\MediaType(
*           mediaType="application/json",
*          )
*      ),
* @OA\Response(
*         response=401,
*         description="Unauthenticated",
*      ),
* @OA\Response(
*         response=403,
*         description="Forbidden"
*      ),
* @OA\Response(
*         response=400,
*         description="Bad Request"
*      ),
* @OA\Response(
*          response=404,
*          description="Not found"
*   ),
* security={
*       {"bearerAuth": {}}
*     }
* )
*/


    public function store(ruleRequest $request)
    {
        try{
            
            $rule = new Rule();
            $rule->fill($request->all());
            $rule->save();
            return [
                'success'   => true,
                'message'   => "Regla agregada con exito",
            ];


        } catch ( Exception $e ){
            
            return [
                'success'   => false,
                'message'   => "Registro Fallido",
            ];
        }
    }

/**
* @OA\GET(
*      path="/api/showRule/{id}",
*      operationId="showRule",
*      tags={"Rules"},
*      summary="Obtener regla especifica",
* @OA\Parameter(
*      name="id",
*      in="path",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
* @OA\Response(
*          response=200,
*          description="Successful operation",
*          @OA\MediaType(
*           mediaType="application/json",
*          )
*      ),
* @OA\Response(
*         response=401,
*         description="Unauthenticated",
*      ),
* @OA\Response(
*         response=403,
*         description="Forbidden"
*      ),
* @OA\Response(
*         response=400,
*         description="Bad Request"
*      ),
* @OA\Response(
*          response=404,
*          description="Not found"
*   ),
* security={
*       {"bearerAuth": {}}
*     }
* )
*/
    public function show($id)
    {
        try{
            
            $rule = Rule::findOrFail($id);
            return $rule;

        }catch(Exception $e){
            
            return [
                'success'   => false,
                'message'   => "Error al mostrar la regla",
            ];
        }
    }

/**
* @OA\DELETE(
*      path="/api/deleteRule/{id}",
*      operationId="deleteRule",
*      tags={"Rules"},
*      summary="Eliminar regla",
* @OA\Parameter(
*      name="id",
*      in="path",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
* @OA\Response(
*          response=200,
*          description="Successful operation",
*          @OA\MediaType(
*           mediaType="application/json",
*          )
*      ),
* @OA\Response(
*         response=401,
*         description="Unauthenticated",
*      ),
* @OA\Response(
*         response=403,
*         description="Forbidden"
*      ),
* @OA\Response(
*         response=400,
*         description="Bad Request"
*      ),
* @OA\Response(
*          response=404,
*          description="Not found"
*   ),
* security={
*       {"bearerAuth": {}}
*     }
* )
*/
    public function destroy($id)
    {
        try{
            $rule = Rule::findOrFail($id);
            $rule->delete();
            return [
                'success'   => true,
                'message'   => "Regla eliminada con exito",
            ];
        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al eliminar la regla",
            ];
        }
    }

/**
* @OA\POST(
*      path="/api/restoreRule/{id}",
*      operationId="restoreRule",
*      tags={"Rules"},
*      summary="Restaurar regla",
* @OA\Parameter(
*      name="id",
*      in="path",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
* @OA\Response(
*          response=200,
*          description="Successful operation",
*          @OA\MediaType(
*           mediaType="application/json",
*          )
*      ),
* @OA\Response(
*         response=401,
*         description="Unauthenticated",
*      ),
* @OA\Response(
*         response=403,
*         description="Forbidden"
*      ),
* @OA\Response(
*         response=400,
*         description="Bad Request"
*      ),
* @OA\Response(
*          response=404,
*          description="Not found"
*   ),
* security={
*       {"bearerAuth": {}}
*     }
* )
*/
    public function restore($id)
    {
        try{
            $rule = Rule::onlyTrashed()->find($id);
            $rule->restore();
            return [
                'success'   => true,
                'message'   => "Regla restaurada con exito",
            ];
        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al restaurar la regla",
            ];
        }
    }
}
