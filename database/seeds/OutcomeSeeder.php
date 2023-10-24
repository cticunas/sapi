<?php

use Illuminate\Database\Seeder;
use PharIo\Manifest\Author;

class OutcomeSeeder extends Seeder
{
    public function solveProjects(){
        $list = \App\Models\Research::whereRaw(" (select count(id) from outcomes o where o.status=1 and o.research_id =research.id and o.type=1)=0
        and (select count(id) from outcomes o where o.status=1 and o.research_id =research.id and o.type=3)>0 ");
        foreach ($list->get() as $research){
            $mes = date("m",strtotime($research->date_init));
            $mes = is_null($mes) ? (int)date('m') : $mes;
            $trim=floor(($mes-1) / 3)+1;
            
            $outcome=\App\Models\Outcome::create([
                "type"=>1,
                "name"=>$research->title,
                "date"=>$research->date_init,
                "research_id"=>$research->id,
                'period_type'=>'T',
                "period"=>$trim,
                "approved"=>1,
                "reviewed"=>1,
                "approved_date"=>$research->date_init,
                "reviewed_date"=>$research->date_init,
                "approved_by"=>1,
                "reviewed_by"=>1,
            ]);    
            $authors=\App\Models\ResearchAuthor::where(["research_id"=>$research->id])->get();
            //print_r($authors->toArray()); 
            foreach ($authors as $author){
                \App\Models\OutcomeAuthor::create([
                    "role" => $author->role,
                    "outcome_id" => $outcome->id,
                    "author_id" => $author->author_id,
                ]);  
            }
        }
    }

    private function bulk($csvFile){
        $firstline = true;
        $i=0;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                $i++;
                //echo "linea $i \n";
                $date1=$data[3];
                if ($date1=="5300-09-06") $date1="1994-09-06";
                $mes = date("m",strtotime($date1));
                $mes = is_null($mes) ?(int) date('m') : $mes;
                $trim=floor(($mes-1) / 3)+1;
                $type=$data[0];
                if($type==1 || $type==3 || $type==4){ $type=3; }
                else if($type==2){$type=4;}
                else if($type==5){$type=5;}
                else if(is_null($type)){
                    $re=\App\Models\Research::find($data[4]); 
                    if($re->research_state_id==4) $type=3;
                    else $type=1;
                }
                else $type=1;
                
                //print_r($data);
                $outcome=\App\Models\Outcome::create([
                    "type"=>$type,
                    "name"=>$data[2],
                    "date"=>$date1,
                    "research_id"=>$data[4],
                    'period_type'=>'T',
                    "period"=>$trim,
                    "approved"=>1,
                    "reviewed"=>1,
                    "approved_date"=>$date1,
                    "reviewed_date"=>$date1,
                    "approved_by"=>1,
                    "reviewed_by"=>1,
                ]);    
                $authors=\App\Models\ResearchAuthor::where(["research_id"=>$data[4]])->get();
                //print_r($authors->toArray()); 
                foreach ($authors as $author){
                    \App\Models\OutcomeAuthor::create([
                        "role" => $author->role,
                        "outcome_id" => $outcome->id,
                        "author_id" => $author->author_id,
                    ]);  
                }
            }
            $firstline = false;
        }
    }

    public function run()
    {
        $csvFile = fopen(base_path("database/data/dataOutcomes.csv"), "r");
        $this->bulk($csvFile);
        fclose($csvFile);
        $this->solveProjects();
    }
}
