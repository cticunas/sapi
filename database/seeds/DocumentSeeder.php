<?php

use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Document::create(['name'=>'Documentos generales','type'=>'tutos']);
        \App\Models\Document::create(['name'=>'Tutoriales','type'=>'docs']);
    }
}
