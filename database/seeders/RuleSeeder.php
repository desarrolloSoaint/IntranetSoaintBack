<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Rule;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rules')->truncate();

        $rule1 = Rule::create([ 'name'  => 'Horario de Trabajo']);

        $rule2 = Rule::create([ 'name'  => 'Horario de Almuerzo']);

        $rule3 = Rule::create([ 'name'  => 'Pausa Activa Diurna']);

        $rule4 = Rule::create([ 'name'  => 'Pausa Activa Tarde']);
    }
}
