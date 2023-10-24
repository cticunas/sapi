<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private function bulk($csvFile){
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
           
            if (!$firstline) {
                \App\Models\Category::create([
                    //"id"=>$data['0'],
                    "name" =>trim($data[1]),
                    "type" => $data[2],
                    "parent_id" => empty($data[3])?null:$data[3],
                    "organization_id"=> empty($data[4])?null:$data[4],
                ]);    
            }
            $firstline = false;
        }
    }

    public function run()
    {
        $csvFile = fopen(base_path("database/data/dataCategories.csv"), "r");
        $this->bulk($csvFile);
        fclose($csvFile);

    }
}
