<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;

class AccessControlExport implements FromCollection,ShouldAutoSize,WithEvents
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {   
        $schedules = DB::table('rule_user')
            ->join('users', 'rule_user.user_id', '=', 'users.id')
            ->join('rules', 'rule_user.rule_id', '=', 'rules.id')
            ->select('rule_user.date','users.email','rules.name','rule_user.start_time','rule_user.finish_time')
            ->orderBy('rule_user.date', 'desc')->get();
        
        return $schedules;
    }

    public function headings():array {
        return [
            'Fecha',
            'Usuario',
            'Horario',
            'Hora Inicio',
            'Hora Fin'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->style('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}
