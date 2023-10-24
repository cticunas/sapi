<?php

use Illuminate\Database\Seeder;

class RenacytSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Renacyt::create(['code'=>'MR-III','name'=>'Maria R. Nivel III', "group"=>"MR"]);    
        \App\Models\Renacyt::create(['code'=>'MR-II','name'=>'Maria R. Nivel II',   "group"=>"MR"]);    
        \App\Models\Renacyt::create(['code'=>'MR-I','name'=>'Maria R. Nivel I',    "group"=>"MR"]);    
        \App\Models\Renacyt::create(['code'=>'CM-I','name'=>'Monge Nivel I',    "group"=>"CM"]);    
        \App\Models\Renacyt::create(['code'=>'CM-II','name'=>'Monge Nivel II',    "group"=>"CM"]);    
        \App\Models\Renacyt::create(['code'=>'CM-III','name'=>'Monge Nivel III',    "group"=>"CM"]);    
        \App\Models\Renacyt::create(['code'=>'CM-IV','name'=>'Monge Nivel IV',    "group"=>"CM"]);    
    }
}
