<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeopleSeeder extends Seeder
{
    private function bulk($csvFile,$type){
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
           
            if (!$firstline) {
                // print_r($data);
                $college_name = $data[5];
                if($college_name == "Escuela Profesional de Mecanica Electrica"){
                    $college_name="Escuela Profesional en Mecanica Electrica";
                }
                if($college_name == "Otra Institucion"){
                    $college_name="Otro -";
                }
                $org= \App\Models\Organization::whereRaw("lower(name)='". strtolower( $college_name)."'" )->first();
                if(!$org) throw new \Exception("Escuela no existe ".$college_name); 
                $condition = $data[4] == 'NOMBRADO' ? 'N': ($data[4] == 'CONTRATADO' ? 'C' : 'O');
               
                \App\Models\Person::create([
                    "id"=>$data[0],
                    "lastname" => trim($data[1]),
                    "name" => str_replace("  "," ", trim($data[2])),
                    "sex" => $data['3'],
                    "condition" => $condition,
                    'organization_id'=>$org->id,
                    "type"=> $type,
                    "degree"=> $type == 'D' ? 'M' : 'O'
                ]);    
            }
            $firstline = false;
        }
    }
    public function run()
    {
        
        \App\Models\Person::create(['name'=>'Martin','lastname'=>"Pardo",'email'=>'martin.pardo@unas.edu.pe','dni'=>'','type'=>'A','organization_id'=>17,'role_id'=>1]);
        \App\Models\Person::create(['name'=>'Jimmy','lastname'=>"Loloy Laurencio",'email'=>'jimmy.loloy@unas.edu.pe','dni'=>'','type'=>'E','organization_id'=>17,'role_id'=>1]);

        $csvFile = fopen(base_path("database/data/dataDocentes.csv"), "r");
        $this->bulk($csvFile,'D');
        fclose($csvFile);
        //Abrir el archivo dataEstudiantes y borrar el primer registro con id=1
        $csvFile = fopen(base_path("database/data/dataEstudiantes.csv"), "r");
        $this->bulk($csvFile,'E');
        fclose($csvFile);  
        $csvFile = fopen(base_path("database/data/dataAdministrativos.csv"), "r");
        $this->bulk($csvFile,'A');
        fclose($csvFile);  
        $csvFile = fopen(base_path("database/data/dataOtros.csv"), "r");
        $this->bulk($csvFile,'O');
        fclose($csvFile); 
        $csvFile = fopen(base_path("database/data/dataPersonMissing.csv"), "r");
        $this->bulk($csvFile,'O');
        fclose($csvFile);

        //postgresql
        $db_driver = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        if( $db_driver=="pgsql" ){
            $last= \App\Models\Person::orderBy("id","desc")->first();
            DB::update(DB::raw("alter sequence people_id_seq restart with  ".($last->id+1) ));
        }
       
    }

}
