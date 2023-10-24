<?php

use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run()
    {

        //AGRO
        \App\Models\Organization::create(['name'=>'Facultad de Agronomia','level'=>'PREGRADO','code'=>'AGRO','creation'=>'2001',]);
        \App\Models\Organization::create(['name'=>'Escuela profesional de Ciencias Agrarias','level'=>'PREGRADO','code'=>'AGRO','creation'=>'2018','parent_id'=>'1']);
        \App\Models\Organization::create(['name'=>'Maestria en Ciencias en Agroecologia','level'=>'POSGRADO','code'=>'AGRO','creation'=>'2018','parent_id'=>'1']);
        \App\Models\Organization::create(['name'=>'Maestria en Ciencias Agricolas','level'=>'POSGRADO','code'=>'AGRO','creation'=>'2018','parent_id'=>'1']);

        //ZOOTECNIA
        \App\Models\Organization::create(['name'=>'Facultad de Zootecnia','level'=>'PREGRADO','code'=>'ZOOT','creation'=>'2005',]);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Pecuarias','level'=>'PREGRADO','code'=>'ZOO','creation'=>'2018','parent_id'=>'5']);
        \App\Models\Organization::create(['name'=>'Maestria en Ciencias Pecuarias','level'=>'POSGRADO','code'=>'ZOO','creation'=>'2018','parent_id'=>'5']);

        //FIIA
        \App\Models\Organization::create(['name'=>'Facultad de Ingenieria en Industrias Alimentarias','level'=>'PREGRADO','code'=>'FIIA','creation'=>'2018',]);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias en Tecnologia e Ingenieria de Alimentos','level'=>'PREGRADO','code'=>'FIIA','creation'=>'2018','parent_id'=>'8']);

        //RECURSOS
        \App\Models\Organization::create(['name'=>'Facultad de Recursos Naturales Renovables','level'=>'PREGRADO','code'=>'FRNR','creation'=>'2018',]);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Recursos Naturales Renovables','level'=>'PREGRADO','code'=>'RNR','creation'=>'2018','parent_id'=>'10']);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Ambientales','level'=>'PREGRADO','code'=>'MA','creation'=>'2018','parent_id'=>'10']);

        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Forestales','level'=>'PREGRADO','code'=>'FOR','creation'=>'2018','parent_id'=>'10']);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias en Conservacion de Suelos y Agua','level'=>'PREGRADO','code'=>'CSA','creation'=>'2018','parent_id'=>'10']);

        \App\Models\Organization::create(['name'=>'Maestria en Ciencias en Gestion de los Recursos Naturales Renovables','level'=>'POSGRADO','code'=>'CSA','creation'=>'2018','parent_id'=>'10']);

        //FIIS
        \App\Models\Organization::create(['name'=>'Facultad de Ingenieria en Informatica y Sistemas','level'=>'PREGRADO','code'=>'FIIS','creation'=>'2018']);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias en Informatica y Sistemas','level'=>'PREGRADO','code'=>'FIIS','creation'=>'2018','parent_id'=>'16']);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Exactas','level'=>'PREGRADO','code'=>'FIIS','creation'=>'2018','parent_id'=>'16']);

        //FIME
        \App\Models\Organization::create(['name'=>'Facultad de Ingenieria en Mecanica Electrica','level'=>'PREGRADO','code'=>'FIME','creation'=>'2018',]);
        \App\Models\Organization::create(['name'=>'Escuela Profesional en Mecanica Electrica','level'=>'PREGRADO','code'=>'FIME','creation'=>'2018','parent_id'=>'19']);

        //CONTA
        \App\Models\Organization::create(['name'=>'Facultad de Ciencias Contables','level'=>'PREGRADO','code'=>'CONTA','creation'=>'2018',]);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Contables','level'=>'PREGRADO','code'=>'CONTA','creation'=>'2018','parent_id'=>'21']);

        //FACEA
        \App\Models\Organization::create(['name'=>'Facultad de Ciencias Economicas y Administrativas','level'=>'PREGRADO','code'=>'FACEA','creation'=>'2001',]);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Economicas','level'=>'PREGRADO','code'=>'FACEA','creation'=>'2018','parent_id'=>'23']);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Administrativas','level'=>'PREGRADO','code'=>'FACEA','creation'=>'2018','parent_id'=>'23']);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Ciencias Humanas','level'=>'PREGRADO','code'=>'FACEA','creation'=>'2018','parent_id'=>'23']);
        \App\Models\Organization::create(['name'=>'Escuela Profesional de Humanidades','level'=>'PREGRADO','code'=>'FACEA','creation'=>'2018','parent_id'=>'23']);
        \App\Models\Organization::create(['name'=>'Maestria en Ciencias Economicas','level'=>'POSGRADO','code'=>'FACEA','creation'=>'2018','parent_id'=>'23']);
        
        //Administrativos
        \App\Models\Organization::create(['name'=>'Area Administrativa','level'=>'PREGRADO','code'=>'AA','creation'=>'2001',]);
        \App\Models\Organization::create(['name'=>'Area Administrativa','level'=>'PREGRADO','code'=>'AA','creation'=>'2001','parent_id'=>'29']);
        
        // Otros
        \App\Models\Organization::create(['name'=>'Otro','level'=>'','code'=>'AA','creation'=>'2001',]);
        \App\Models\Organization::create(['name'=>'Otro -','level'=>'','code'=>'AA','creation'=>'2001','parent_id' => '31']);

        //Posgrado
        \App\Models\Organization::create(['name'=>'Unidad de Posgrado','level'=>'','code'=>'AA','creation'=>'2001',]);
        \App\Models\Organization::create(['name'=>'Escuela de Posgrado','level'=>'','code'=>'AA','creation'=>'2001','parent_id' => '33']);
    }
}