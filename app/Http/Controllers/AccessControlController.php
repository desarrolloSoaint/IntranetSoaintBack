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
                ->select('rule_user.date','users.email','rules.name','rule_user.start_time','rule_user.finish_time')
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

            $data  = DB::table('rule_user')->where('rule_id', $request['rule_id'])->where('user_id',$request['user_id'])
                        ->orderBy('date', 'desc')->get();
            
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
            
            $schedule = DB::table('rules')
                ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
                ->select('schedule.id','rules.name','schedule.start_time','schedule.finish_time')
                ->where('rules.name','=',$request['rule_name'])
                ->where('schedule.status','=','Activo')
                ->orderBy('name')
                ->get()->first();

            $start = Carbon::parse($schedule->start_time);
            $finish = Carbon::parse($schedule->finish_time);
            
            $data  = DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                                            ->where('date',$date)->get()->first();

            if ($time_converter->between($start, $finish, true)){
                if($data != null){
                    return [
                        'success'   => false,
                        'message'   => "Ya registro la hora de entrada para el dia de hoy",
                    ];

                }else{
                    $user = User::find($request['user_id']);
                    $user->rules()->attach($rule->id,['date'=>$date, 'start_time'=>$time]);
                    return [
                        'success'   => true,
                        'message'   => "Hora Registrada",
                    ];
                }
            }else{
                return [
                    'success'   => false,
                    'message'   => "Fuera del Horario",
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
            
            $schedule = DB::table('rules')
                ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
                ->select('schedule.id','rules.name','schedule.start_time','schedule.finish_time')
                ->where('rules.name','=',$request['rule_name'])
                ->where('schedule.status','=','Activo')
                ->orderBy('name')
                ->get()->first();

            $start = Carbon::parse($schedule->start_time);
            $finish = Carbon::parse($schedule->finish_time);
            $duration = $finish->diffInHours($start);

            $data  = DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                                            ->where('date',$date)->get()->first();

            if($data == null){
                return [
                    'success'   => false,
                    'message'   => "Primero debe registrar una hora de inicio",
                ];
            }else if ($data->finish_time != null){
                return [
                    'success'   => false,
                    'message'   => "Ya registro una hora de fin",
                ];
            }else{

                $currentStart = $data->start_time;
                $currentStart = Carbon::parse($currentStart)->addHours($duration);

                if($time_converter >= $currentStart){
                    DB::table('rule_user')->where('rule_id', $rule->id)->where('user_id',$request['user_id'])
                    ->where('date',$date)->update(['finish_time' => $time]);
                    return [
                        'success'   => true,
                        'message'   => "Hora Registrada",
                    ];
                }else {
                    return [
                        'success'   => true,
                        'message'   => "No disponible todavia"
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
