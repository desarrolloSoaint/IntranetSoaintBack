<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schedule1 = Schedule::create([ 'start_time'    =>  '08:00',
                                        'finish_time'   =>  '15:00',
                                        'status'        =>  'Activo',
                                        'rule_id'       =>  '1']);
        
        $schedule2 = Schedule::create([ 'start_time'    =>  '12:00',
                                        'finish_time'   =>  '13:00',
                                        'status'        =>  'Activo',
                                        'rule_id'       =>  '2']);

        $schedule3 = Schedule::create([ 'start_time'    =>  '10:00',
                                        'finish_time'   =>  '10:10',
                                        'status'        =>  'Activo',
                                        'rule_id'       =>  '3']);

        $schedule4 = Schedule::create([ 'start_time'    =>  '14:00',
                                        'finish_time'   =>  '14:10',
                                        'status'        =>  'Activo',
                                        'rule_id'       =>  '4']);
    }
}
