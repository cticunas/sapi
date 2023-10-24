<?php

use Illuminate\Database\Seeder;

class RTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rtypes')->insert(['code' => 'TS','name'=>'Tesis']);
        DB::table('rtypes')->insert(['code' => 'ID','name'=>'Investigacion Docente']);
    }
}
