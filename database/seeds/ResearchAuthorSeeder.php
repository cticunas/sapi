<?php

use Illuminate\Database\Seeder;

class ResearchAuthorSeeder extends Seeder
{    
    private function bulk($csvFile){
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
           
            if (!$firstline) {
                //print_r($data);
                $role = $data[1];
                if($role == "Tesista" || $role=="Titular"){
                    $rol="TI";
                }
                if($role == "Asesor" || $role=="Co-Asesor"){
                    $rol="AS";
                }
                if($role == "Miembro ordinario"){
                    $rol="TI";
                }
                if($role == "Miembro extraordinario"){
                    $rol="EO";
                }
                if($role == "Otros"){
                    $rol="OT";
                }
                if($role == "Asesor(es) externo"){
                    $rol="CO";
                }
                
                \App\Models\ResearchAuthor::create([
                    "id"=>$data[4],
                    "role" => $rol,
                    "author_id" => $data['2'],
                    "research_id" => $data['0']
                ]);
                if($rol=='TI'){
                    $p=App\Models\Person::find($data[2]);
                    App\Models\Research::where('id',$data[0])->update(['organization_id'=>$p->organization_id]);
                    
                }    
            }
            $firstline = false;
        }
    }
    public function run()
    {
        $csvFile = fopen(base_path("database/data/dataResearchAuthors.csv"), "r");
        $this->bulk($csvFile);
        fclose($csvFile);

        $db_driver = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        if( $db_driver=="pgsql" ){
            $last= \App\Models\ResearchAuthor::orderBy("id","desc")->first();
            DB::update(DB::raw("alter sequence research_authors_id_seq restart with  ".($last->id+1) ));
        }
    }
}
