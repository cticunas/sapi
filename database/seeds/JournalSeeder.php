<?php

use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
   public function run()
    {
         \App\Models\Journal::create(['code'=>'JIISIC','name'=>"Jornadas Iberoamericana de Ingenieria de Software","type"=>"CO","indexed"=>"Scielo","url"=>'']);   
    }
}
