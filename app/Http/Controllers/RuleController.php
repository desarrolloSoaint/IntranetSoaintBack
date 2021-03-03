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
*     summary="Obtener reglas",
*     tags={"Rules"},
* @OA\Response(
*         response=200,
*         description="Reglas Obtenidas con Exito"
*     ),
* @OA\Response(
*         response="422",
*         description="Error al Obtener las Reglas"
*     ),
*     security={
*       {"bearerAuth": {}}
*     }
* )
*/

    public function index()
    {
        $rule = Rule::orderBy('id','Desc')->paginate(10);

        return $rule;
    }

/**
* @OA\POST(
*     path="/api/addRules",
*     summary="Registrar regla",
*     tags={"Rules"},
* @OA\RequestBody(
*    required=true,
*    description="Introducir Datos",
*    @OA\JsonContent(
*       required={"name","description"},
*       @OA\Property(property="name", type="string", format="text", example="Hora de Entrada"),
*       @OA\Property(property="description", type="string", format="text", example="8:00 AM") 
*    ),
* ),
* @OA\Response(
*         response=200,
*         description="Regla Registrada"
*     ),
* @OA\Response(
*         response="422",
*         description="Error al Registrar la Regla"
*     ),
*     security={
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
