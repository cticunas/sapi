<?php

use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Plan::create(
            ['code'=>'PLA001','name'=>'Plan de investigacion 2013-2021','resolution'=>'Resolucion N°002','init'=>'2013','end'=>'2021','active'=>0]
        );
        \App\Models\Plan::create(
            ['code'=>'PLN002','name'=>'Finanzas','resolution'=>'RESOLUCIÓN N° 059-2013-CU-R-UNAS','init'=>'2013','end'=>'2014','active'=>0]
        );
        \App\Models\Plan::create(
            ['code'=>'PLN003','name'=>'Programas y Lineas de investigacion 2018-2021','resolution'=>'Resolucion N°004','init'=>'2018','end'=>'2021','active'=>1]
        );
    }
}
