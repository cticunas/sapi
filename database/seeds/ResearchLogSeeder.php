<?php

use Illuminate\Database\Seeder;

class ResearchLogSeeder extends Seeder
{
    private function bulk($csvFile){
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                //print_r($data);
                \App\Models\ResearchLog::create([
                    "date_at"=>$data[2],
                    "new_status_id"=>$data[1],
                    "research_id"=>$data[0],
                    "user_id"=>1,
                    "note"=>$data[3],
                ]);      
            }
            $firstline = false;
        }
    }
    
    public function run()
    {
        $csvFile = fopen(base_path("database/data/dataResearchLogs.csv"), "r");
        $this->bulk($csvFile);
        fclose($csvFile);
    }
}
