<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(['code' => 'admin','name'=>"Administrador"]);
        DB::table('roles')->insert(['code' => 'research', "name"=>"Investigador"]);
        DB::table('roles')->insert(['code' => 'research_unit', "name"=>'Unidad de Investigacion']);
        // DB::table('roles')->insert(['code' => 'degree_commission',"name"=>"Comision Grados"]);
        DB::table('roles')->insert(['code' => 'dgi',"name"=>"Direccion Investigacion"]);
        DB::table('roles')->insert(['code' => 'coordinator','name'=>"Coordinador de Grupo"]);
    }
}
