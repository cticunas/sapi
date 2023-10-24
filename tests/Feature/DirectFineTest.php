<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DirectFineTest extends TestCase
{
    // use DatabaseMigrations {
    //     runDatabaseMigrations as baseRunDatabaseMigrations;
    // }
    // public function runDatabaseMigrations()
    // {
    //     $this->baseRunDatabaseMigrations();
    //     $this->artisan('db:seed');
    // }
    private $data;

    public function setUp():void{
        parent::setUp();
        echo " => \e[1m Direct Debt: \e[0m";
    
    $this->data=[
        'code'=>'00001',
        'date_at'=>'2020-07-30 10:10:10',
        'total'=>20.5,
        'type'=>'direct',
        'stand_id'=>1,
        'concept_id'=>1,
        'client_id'=>1,
    ];
}

   
    public function testnew(){
        echo "test => \e[32m nuevo\e[0m, ";
        $r = $this->json('POST', '/api/debt',$this->data);
        // $r->assertStatus(201);

        dd($r->content());

        echo "\e[32m editar\e[0m \n";
        $this->data['total']=30.00;
        $id=json_decode($r->content())->id;
        $response = $this->json('PUT', "/api/debt/$id",$this->data);
        $response->assertStatus(200);

    }

    public function testlist(){
        echo "test => \e[32m listar\e[0m \n";
        //$r = $this->get('/api/debt/merced');
        $r = $this->get('/api/debt/direct');
        dd($r->content());
        $r->assertStatus(200);
    }
    
    // public function testsearch(){
    //     echo "test => \e[32m buscar \e[0m \n";
    //     $r = $this->get('/api/debt?search=fredy');
    //     // dd($r->content());
    //     $r->assertStatus(200);
    // }
    

}
