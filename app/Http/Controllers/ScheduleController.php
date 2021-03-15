<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Schedule\scheduleRequest;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;

class ScheduleController extends Controller
{
/**
* @OA\GET(
*     path="/api/getSchedules",
*     operationId="getSchedules",
*     summary="Obtener horarios",
*     tags={"Schedules"},
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
            $schedules = DB::table('rules')
            ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
            ->select('schedule.id','rules.name','schedule.start_time','schedule.finish_time','schedule.status')
            ->where('schedule.deleted_at','=',null)
            ->orderBy('status')
            ->get();
            return $schedules;
        
        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al obtener los horarios",
            ];
        }
    }

/**
* @OA\GET(
*      path="/api/showSchedule/{id}",
*      operationId="showSchedule",
*      tags={"Schedules"},
*      summary="Obtener horario especifico",
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
            
            $schedule = Schedule::findOrFail($id);
            return $schedule;

        }catch(Exception $e){
            
            return [
                'success'   => false,
                'message'   => "Error al obtener el horario",
            ];
        }
    }

/**
* @OA\POST(
*     path="/api/addSchedule",
*     operationId="addShedule",
*     summary="Registrar horario",
*     tags={"Schedules"},
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


    public function store(scheduleRequest $request)
    {
        try{
            
            $start = Carbon::parse($request->input('start_time'));
            $finish = Carbon::parse($request->input('finish_time'));

            if($start < $finish){

                $schedule = new Schedule();
                $schedule->fill($request->all());
                $schedule->save();
                return [
                    'success'   => true,
                    'message'   => "Horario agregado con exito",
                ];

            }else{
                return [
                    'success'   => false,
                    'message'   => "La hora final tiene que ser mayor que la inicial",
                ];
            }


        } catch ( Exception $e ){
            
            return [
                'success'   => false,
                'message'   => "Registro Fallido",
            ];
        }
    }

/**
* @OA\PUT(
*      path="/api/updateSchedule/{id}",
*      operationId="updateSchedule",
*      tags={"Schedules"},
*      summary="Modificar horario",
* @OA\Parameter(
*      name="id",
*      in="path",
*      required=true,
*      @OA\Schema(
*           type="integer"
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
    public function update(scheduleRequest $request, $id)
    {
        try{
            
            $schedule = Schedule::find($id);

            $schedule->update([ 'start_time'    => $request->input('start_time'),
                                'finish_time'   => $request->input('finish_time')]);

            return [
                'success'   => true,
                'message'   => "Horario modificado con exito",
            ];

        }catch(Exeption $e){
                
            return [
                'success'   => false,
                'message'   => "Error al modificar",
            ];
        }
    }

/**
* @OA\DELETE(
*      path="/api/deleteSchedule/{id}",
*      operationId="deleteSchedule",
*      tags={"Schedules"},
*      summary="Eliminar Horario",
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
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return [
            'success'   => true,
            'message'   => "Horario eliminado con exito",
        ];
    }catch(Exception $e){
        return [
            'success'   => false,
            'message'   => "Error al eliminar",
        ];
    }
}

/**
* @OA\POST(
*      path="/api/restoreSchedule/{id}",
*      operationId="restoreSchedule",
*      tags={"Schedules"},
*      summary="Restaurar horario",
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
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->restore();
        return [
            'success'   => true,
            'message'   => "Horario restaurado con exito",
        ];
    }catch(Exception $e){
        return [
            'success'   => false,
            'message'   => "Error al restaurar el horario",
        ];
    }
}

/**
* @OA\POST(
*      path="/api/statusSchedule/{id}",
*      operationId="statusSchedule",
*      tags={"Schedules"},
*      summary="Cambiar el estatus del horario",
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
    public function changeStatus($id)
    {
        try{
            $schedule = Schedule::find($id);
            $isActive = DB::table('rules')
            ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
            ->select('schedule.id')
            ->where('schedule.rule_id','=',$schedule->rule_id)
            ->where('schedule.status','=','Activo')
            ->get()->first();

            if($isActive != null ){
                $scheduleActive = Schedule::find($isActive->id);
                $scheduleActive->status = "Inactivo";
                $scheduleActive->save();

                $schedule->status = "Activo";
                $schedule->save();

            }else{
                $schedule->status = "Activo";
                $schedule->save();
            }

            return [
                'success'   => true,
                'message'   => "Estatus cambiado con exito"

            ];
        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al cambiar el estatus",
            ];
        }
    }
/**
* @OA\GET(
*     path="/api/activeSchedules",
*     operationId="activeSchedules",
*     summary="Obtener horarios activos",
*     tags={"Schedules"},
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

    public function activeSchedules()
    {
        try{

            $schedules = DB::table('rules')
            ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
            ->select('schedule.id','rules.name','schedule.start_time','schedule.finish_time')
            ->where('schedule.status','=','Activo')
            ->orderBy('name')
            ->get();
            
            return $schedules;
        
        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al obtener los horarios",
            ];
        }
    }

/**
* @OA\GET(
*     path="/api/inactiveSchedules",
*     operationId="inactiveSchedules",
*     summary="Obtener horarios inactivos",
*     tags={"Schedules"},
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

    public function inactiveSchedules()
    {
        try{
            
            $schedules = DB::table('rules')
            ->join('schedule', 'rules.id', '=', 'schedule.rule_id')
            ->select('schedule.id','rules.name','schedule.start_time','schedule.finish_time','schedule.status')
            ->where('schedule.status','=','Inactivo')
            ->orderBy('name')
            ->get();

            return $schedules;
        
        }catch(Exception $e){
            return [
                'success'   => false,
                'message'   => "Error al obtener los horarios",
            ];
        }
    }
}
