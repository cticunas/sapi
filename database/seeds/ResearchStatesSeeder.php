<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResearchStatesSeeder extends Seeder
{
    public function run()
    {
        DB::table('research_states')->insert(['name'=>"No definido"]);
        DB::table('research_states')->insert(['name'=>"Nuevo"]);
        DB::table('research_states')->insert(['name'=>"En Ejecución"]);
        DB::table('research_states')->insert(['name'=>"Culminado"]);
        DB::table('research_states')->insert(['name'=>"Suspendido"]);
        DB::table('research_states')->insert(['name'=>"Anulado"]);
    }
}
