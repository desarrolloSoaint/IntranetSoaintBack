<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rule;
use App\Models\AccessControl;
use App\Exports\AccessControlExport;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Excel;
use Validator;

class AccessControlController extends Controller
{


/**
* @OA\GET(
*      path="/api/getHistory",
*      operationId="getHistory",
*      tags={"Access Control"},
*      summary="Obtener Historial",
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

    public function getHistory(){
        try{

            $schedules = DB::table('rule_user')
                ->join('users', 'rule_user.user_id', '=', 'users.id')
                ->join('rules', 'rule_user.rule_id', '=', 'rules.id')
                ->select('rule_user.date','users.email','rules.name','rule_user.start_time','rule_user.finish_time','rule_user.observation')
                ->orderBy('rule_user.date', 'desc')->get();
            
            return $schedules;

        }catch(Exception $e){
            
            return [
                'success'   => false,
                'message'   => "Error al traer la data",
            ];
        }

    }

/**
* @OA\POST(
*      path="/api/getCurrentUserHistory",
*      operationId="getCurrentUserHistory",
*      tags={"Access Control"},
*      summary="Obtener Historial del Usuario actualmente en sesion",
* @OA\Parameter(
*      name="user_id",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
*  @OA\Parameter(
*      name="rule_name",
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

    public function getCurrentUserHistory(Request $request){
        try{

            $rule = Rule::where('name',$request['rule_name'])->get()->first();
            $data = DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                    ->orderBy('date','desc')->get();
            
            return $data;

        }catch(Exception $e){
            
            return [
                'success'   => false,
                'message'   => "Error al traer la data",
            ];
        }

    }

/**
* @OA\POST(
*      path="/api/getHistoryByUserAndRule",
*      operationId="getHistoryByUserAndRule",
*      tags={"Access Control"},
*      summary="Obtener Historial filtrado por Usuario y Regla",
* @OA\Parameter(
*      name="user_id",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
*  @OA\Parameter(
*      name="rule_id",
*      in="query",
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

    public function getHistoryByUserAndRule(Request $request){
        try{

            $data = $request->only('user_id','rule_id');

            $validator = Validator::make($data,[
                'user_id'       => 'required',
                'rule_id'       => 'required'
            ],$messages = [
                'user_id.required' => 'El usuario es requerido',
                'rule_id.required' => 'El tipo de horario es requerido',
            ]);
    
            if ($validator->fails()){
                $errors = $validator->errors();
                return response()->json([
                    'success'  => false,
                    'error'  => $errors->all()
                ],422);
            }
            
            $schedules = DB::table('rule_user')
                ->join('users', 'rule_user.user_id', '=', 'users.id')
                ->join('rules', 'rule_user.rule_id', '=', 'rules.id')
                ->select('rule_user.date','users.email','rules.name','rule_user.start_time','rule_user.finish_time')
                ->where('rule_user.rule_id','=',$request['rule_id'])
                ->where('rule_user.user_id','=',$request['user_id'])
                ->orderBy('rule_user.date', 'desc')->get();

            return $schedules;

        }catch(Exception $e){
            
            return [
                'success'   => false,
                'message'   => "Error al traer la data",
            ];
        }

    }


/**
* @OA\POST(
*      path="/api/registerStartTime",
*      operationId="registerStartTime",
*      tags={"Access Control"},
*      summary="Registrar Hora de Inicio",
* @OA\Parameter(
*      name="user_id",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
*  @OA\Parameter(
*      name="rule_name",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="date",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="time",
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

    public function registerStartTime(Request $request){
        
        try{
            
            $date = Carbon::now()->setTimezone('America/Caracas')->toDateString();
            $time = Carbon::now()->setTimezone('America/Caracas')->format('H:i');
            // $date = $request['date'];
            // $time = $request['time'];
            $time_converter = Carbon::parse($time);
            
            $rule = Rule::where('name',$request['rule_name'])->get()->first();
            
            //Obtenemos el horario activo.
            $schedule = DB::table('rules')
                ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
                ->select('schedule.start_time','schedule.finish_time')
                ->where('rules.name','=',$request['rule_name'])
                ->where('schedule.status','=','Activo')
                ->orderBy('name')
                ->get()->first();
            
            //Obtenemos la hora de inicio, fin del horario activo.
            $start = Carbon::parse($schedule->start_time);
            $finish = Carbon::parse($schedule->finish_time);
            $period = Carbon::parse($schedule->start_time);

            //Obtenemos el horario de trabajo activo para validar en caso de ser pausa activa.
            $entry_schedule = DB::table('rules')
                ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
                ->select('schedule.start_time')
                ->where('rules.name','=','Horario de Trabajo')
                ->where('schedule.status','=','Activo')
                ->orderBy('name')
                ->get()->first();

            //Se proporciona un periodo valido de ingreso desde la hora de inicio.
            if($rule->name == "Horario de Trabajo"){
                $period = $period->addMinutes(45);
            }else if ($rule->name == "Horario de Almuerzo"){
                $period = $period->addMinutes(15);
            }else{
                $period = $period->addMinutes(5);
            }
            
            // Se busca para saber si ya se ingreso un registro.
            $data  = DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                                            ->where('date',$date)->get()->first();
            
            // Obtenemos el registro de Hora de Entrada para validar en caso de ser pausa activa.
            $data_entry  = DB::table('rule_user')->where('rule_id', 1)->where('user_id',$request['user_id'])
                                            ->where('date',$date)->get()->first();
            
            //En caso de obtener una Hora de Entrada, la convertimos en formato Date para poder comparar.
            if($data_entry){
                $data_entry_start = Carbon::parse($data_entry->start_time);
            }else{
                $data_entry_start = null;
            }

            //La hora ingresada tiene que estar entre la hora inicio y fin del horario activo.
            if ($time_converter->between($start,$finish,true)){

                //Verificamos si la hora ingresada corresponde a una pausa activa.
                $contains = Str::contains($request['rule_name'], 'Pausa');

                //Si no ingreso hora de entrada o la ingreso media hora despues de la hora establecida, no posee pausa activa.
                if($contains && ($data_entry_start == null || $data_entry_start > Carbon::parse($entry_schedule->start_time)->addMinutes(30))){
                    
                    return [
                        'success'   => false,
                        'message'   => "Usted no posee pausa activa el dia de hoy.",
                        'observation'   => false
                    ];

                }else{
                    //No puede ingresar una hora de inicio si ya existe una para ese dia.
                    if($data != null){
                        return [
                            'success'   => false,
                            'message'   => "Ya registro la hora de inicio para el dia de hoy",
                            'observation'   => false
                        ];
    
                    }else{
                        //Tiene un periodo de tiempo despues de la hora inicial establecida para ingresar la hora.
                        if ($time_converter->between($start,$period,true)){
                            
                            $user = User::find($request['user_id']);
                            $user->rules()->attach($rule->id,['date'=>$date,
                                                              'start_time'=>$time,
                                                              'finish_time'=>'No ha registrado',
                                                              'observation'=>'Ninguna']);
                            
                            return [
                                'success'   => true,
                                'message'   => "Hora Registrada",
                                'observation'   => false
                            ];
    
                        }else{

                            //Al ingresar despues del periodo, tiene que indicar la causa del retraso.
                            //Solo horario de trabajo y almuerzo.
                            if (!$contains) {
                    
                                $user = User::find($request['user_id']);
                                $user->rules()->attach($rule->id,['date'=>$date,
                                                                  'start_time'=>$time,
                                                                  'finish_time'=>'No ha registrado',
                                                                  'observation'=>'Ninguna']);
    
                                return [
                                    'success'   => true,
                                    'message'   => "Observacion del Retraso",
                                    'observation'   => true
                                ];

                            }else{
                                //Para la pausa activa no es necesario indicar la observacion del retraso.
                                $user = User::find($request['user_id']);
                                $user->rules()->attach($rule->id,['date'=>$date,
                                                                  'start_time'=>$time,
                                                                  'finish_time'=>'No ha registrado',
                                                                  'observation'=>'Ninguna']);
    
                                return [
                                    'success'   => true,
                                    'message'   => "Hora Registrada",
                                    'observation'   => false
                                ];                                
                            }
                        }
                    }
                }

            }else{
                return [
                    'success'   => false,
                    'message'   => "Fuera del Horario",
                    'observation'   => false
                ];
            }

        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al registrar la hora",
            ];
        }       
    }

/**
* @OA\POST(
*      path="/api/registerFinishTime",
*      operationId="registerFinishTime",
*      tags={"Access Control"},
*      summary="Registrar Hora Fin",
* @OA\Parameter(
*      name="user_id",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
*  @OA\Parameter(
*      name="rule_name",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="date",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="time",
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

    public function registerFinishTime(Request $request){
        try{
            
            $date = Carbon::now()->setTimezone('America/Caracas')->toDateString();
            $time = Carbon::now()->setTimezone('America/Caracas')->format('H:i');
            // $date = $request['date'];
            // $time = $request['time'];
            $time_converter = Carbon::parse($time);
            
            $rule = Rule::where('name',$request['rule_name'])->get()->first();
            
            //Obtenemos el horario activo.
            $schedule = DB::table('rules')
                ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
                ->select('schedule.start_time','schedule.finish_time')
                ->where('rules.name','=',$request['rule_name'])
                ->where('schedule.status','=','Activo')
                ->orderBy('name')
                ->get()->first();

            //Obtenemos la hora de inicio, fin y diferencia entre ellos en el horario activo.
            $start = Carbon::parse($schedule->start_time);
            $finish = Carbon::parse($schedule->finish_time);
            $duration = $finish->diffInHours($start);

            //Verificamos si ya existe un registro que deberia tener una hora de inicio ya ingresada.
            $data = DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                                        ->where('date',$date)->get()->first();

            //Si existe el registro, tiene que ingresar primero la hora de inicio.
            if($data == null){
                return [
                    'success'   => false,
                    'message'   => "Primero debe registrar una hora de inicio",
                    'observation'   => false
                ];

                //Se verifica si ya se encuentra una hora fin ingresada.
            }else if ($data->finish_time != 'No ha registrado'){
                return [
                    'success'   => false,
                    'message'   => "Ya registro una hora de fin",
                    'observation'   => false
                ];
            }else{

                //Se establece la duracion valida con respecto a la hora de inicio
                $period = Carbon::parse($data->start_time)->addHours($duration);

                if($request['rule_name'] == "Horario de Trabajo"){
                    //Solo para Horario de Trabajo
                    if($time_converter >= $period){
                        
                        DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                                                ->where('date',$date)->update(['finish_time' => $time]);
                        
                        return [
                            'success'   => true,
                            'message'   => "Hora Registrada",
                            'observation'   => false
                        ];

                    }else if ($time_converter->between($data->start_time,$period,true)){

                        //Si registra una hora fin antes de cumplirse el periodo, debe indicar la observacion.
                        DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                        ->where('date',$date)->update(['finish_time' => $time]);

                        return [
                            'success'   => true,
                            'message'   => "Observacion de la salida",
                            'observation'   => true    
                        ];

                    }
                }else{

                    //Solo para Horario de Almuerzo y Pausa Activa
                    DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                    ->where('date',$date)->update(['finish_time' => $time]);
                    
                    return [
                        'success'   => true,
                        'message'   => "Hora Registrada",
                        'observation'   => false
                    ];
                }
            }

        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al registrar la hora",
            ];
        }       
    }

/**
* @OA\POST(
*      path="/api/addObservation",
*      operationId="addObservation",
*      tags={"Access Control"},
*      summary="Agregar una observacion",
* @OA\Parameter(
*      name="user_id",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="integer"
*      )
*   ),
*  @OA\Parameter(
*      name="rule_name",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="text",
*      in="query",
*      required=true,
*      @OA\Schema(
*           type="string"
*      )
*   ),
*  @OA\Parameter(
*      name="date",
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
    public function addObservation(Request $request){
        try{
            $date = Carbon::now()->setTimezone('America/Caracas')->toDateString();
            // $date = $request['date'];

            $rule = Rule::where('name',$request['rule_name'])->get()->first();

            //Obtenemos el registro donde se desea agregar la observacion.
            $data = DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                        ->where('date',$date)->get()->first();

            if ($data->observation == 'Ninguna'){

                $new_text= "-".ucfirst($request['text']);

                DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                    ->where('date',$date)->update(['observation' => $new_text]);

                return [
                    'success'   => true,
                    'message'   => "Observación registrada",
                ];

            }else{

                $new_text = $data->observation." -".ucfirst($request['text']);
                
                DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                    ->where('date',$date)->update(['observation' => $new_text]);
                
                return [
                    'success'   => true,
                    'message'   => "Observación registrada",
                ];
            }
        }catch(Exception $e){
            
            return [
                'success'   => false,
                'message'   => "Error al agregar la observacion",
            ];
        }
    }

/**
* @OA\GET(
*      path="/api/clearHistory",
*      operationId="clearHistory",
*      tags={"Access Control"},
*      summary="Limpiar Historial",
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

public function clearHistory(){
    try{

        AccessControl::truncate();
        return [
            'success'   => true,
            'message'   => "Exito al limpiar la data",
        ];
    }catch(Exception $e){
        return [
            'success'   => false,
            'message'   => "Error al limpiar la data",
        ];
    }       
}


/**
* @OA\GET(
*      path="/api/export",
*      operationId="export",
*      tags={"Access Control"},
*      summary="Exportar el Historial",
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

    public function export(){
        try{

            return Excel::download(new AccessControlExport,
                     'export-historial-'.Carbon::now()->setTimezone('America/Caracas')->toDateString().'.xlsx');

        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al exportar la data",
            ];
        }       
    }

}
