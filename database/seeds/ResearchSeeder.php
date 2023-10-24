<?php

use Illuminate\Database\Seeder;

class ResearchSeeder extends Seeder
{
    private function bulk($csvFile){
        $firstline = true;
        $i=0;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            $i++;
            if (!$firstline){
                // print_r($data);
                if(count($data)<12 ) continue;
                $program_name=$data[13];
                $line_name=$data[14];

                preg_match('/(\w+).(\w+)\/(\d+)/i',$data[1], $matches) ;
                    $faculty_code = $matches[2];
                    $organization_id = 0;
                    switch($faculty_code){
                        case 'AGR': $organization_id = 1; break;
                        case 'ZTC': $organization_id = 5; break;
                        case 'IIA': $organization_id = 8; break;
                        case 'AMB': $organization_id = 10; break;
                        case 'FRS': $organization_id = 10; break;
                        case 'CSA': $organization_id = 10; break;
                        case 'RNR': $organization_id = 10; break;
                        case 'IIS': $organization_id = 16; break;
                        case 'MEC': $organization_id = 19; break;
                        case 'CNT': $organization_id = 21; break;
                        case 'ADM': $organization_id = 23; break;
                        case 'ECN': $organization_id = 23; break;
                        case 'HUM': $organization_id = 23; break;
                        case 'ND': $organization_id = 31; break;
                        case 'EPG': $organization_id = 33; break;
                    }
                $group_name=trim($data[13]);
                $line_name=trim($data[14]);
                if($group_name=="Modelos y aplicaciones matematicas discretas y continuos"){
                    $group_name="Modelos y aplicaciones matematicas discretas y continuas";
                }

                if($group_name=="Modelos matematicos y tecnicos numericos computacionales."){
                    $group_name="Modelos matematicos y tecnicas numericas computacionales.";
                }

                if($group_name=="Tecnologias de Informacion (TI)"){
                    $group_name="Tecnologias de Informacion";
                }
                if($group_name=="Sistemas de Informacion (SI)"){
                    $group_name="Sistemas de Informacion";
                }
                if($group_name=="Ingenieria de Software"){
                    $group_name="Ingenieria de Software - GINSOFT";
                }
                if($group_name=="Sistemas de Informacion"){
                    $group_name="Sistemas de Informacion - GISI";
                }
                if($group_name=="Gestion de Boques y Plantaciones Forestales"){
                    $group_name="Gestion de Bosques y Plantaciones Forestales";
                }
                
                //$group_name_ = strtolower($group_name);
                $group_name = str_replace("  "," ",$group_name);
                $group= \App\Models\Category::whereRaw(" lower(name)=lower('$group_name') and type='Grupo' and organization_id=$organization_id " )->first();
                if(!$group) {
                    $group=\App\Models\Category::create(['name'=>$group_name, "type"=>"Grupo", 'organization_id'=>$organization_id,'comment'=>'create from research']);
                }

                if($line_name=="Ecuaciones diferenciales y | sistemas dinamicos") 
                    $line_name= "Ecuaciones diferenciales y sistemas dinamicos";
                $line_name = str_replace("  "," ",$line_name);

                $line= \App\Models\Category::whereRaw(" lower(name) = lower('$line_name') and type='Linea' and parent_id=$group->id")->first();
                if(!$line) {
                    $line=\App\Models\Category::create(['name'=>$line_name,'code'=>'L'.$group->id,"type"=>"Linea","parent_id"=>$group->id,'comment'=>'create from research']);
                   // echo('Linea creada: '.$line_name."\n");
                }
                $state=$data[12];
                $type=trim($data[7]);
                $incentive=0;
                if($type==2) $incentive=1;
                if(is_null($state) || $state=='NULL') $state=1;
                $obj=explode('|',$data[11]);
                //dd($obj);
                $objs=array_map(function($e, $i){return['id'=>$i+1,'name'=>$e];},$obj, array_keys($obj));
                if($data[9]==12)$data[9] = 3; 
                if($data[9]==11)$data[9] = 2;
                \App\Models\Research::create([
                    "id"=>$data[0],
                    "code" => $data['1'],
                    "title" => $data['2'],
                    "date_init" => $data['3'],
                    "date_end" => $data['4'],
                    "budget"=>$data['5'],
                    "grade"=>$data['6'],
                    "type_research"=>$data['7'],
                    "document"=>$data['8'],
                    "plan"=>$data[9],
                    "location"=>$data['10'],
                    "incentive"=>$incentive,
                    "objectives"=> json_encode($objs),
                    "research_state_id"=>$state,
                    "group_id"=>$group->id,//$data['13']
                    "line_id"=>$line->id//$data['14']
                ]);    
            }
            $firstline = false;
        }    
    }
    public function run()
    {
        $csvFile = fopen(base_path("database/data/dataResearch.csv"), "r");
        $this->bulk($csvFile);
        fclose($csvFile);
   
        \App\Models\Research::create([
            'id'=>'1505',
            'code'=>'T.AMB/2014.1/009',
            'title'=>'Corrección del pH de aguas residuales de actividades minero metalúrgicas mediado por Consorcios Bacterianos en biorreactores de lecho escurrido tipo Air LIft en soportes de PVC',
            'budget'=>'9397',
            'fin_type'=>'',
            'fin_company'=>'',
            'date_init'=>'2013-12-01',
            'date_end'=>'2014-05-31',
            'document'=>'Carta FRNR N° 141-2014-UNAS/Resolución N° 005-14-D-UNAS',
            'grade'=>'1',
            'type_research'=>'1',
            'plan'=>3,
            //Pertenece a Ingenieria Ambiental
            'group_id'=>'337',
            'line_id'=>'338',
        ]);

        for ($i=1; $i <= 3; $i++) { 
            \App\Models\Category::where([['type', '=', 'Linea']])->each(function($category) use ($i){
                \App\Models\PlanCategories::create(['plan_id'=>$i,'category_id'=>$category->id]);
            });
        }

         //postgresql
         $db_driver = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
         if( $db_driver=="pgsql" ){
            $last= \App\Models\Research::orderBy("id","desc")->first();
            DB::update(DB::raw("alter sequence research_id_seq restart with  ".($last->id+1) ));
        }
    } 
}