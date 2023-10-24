<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RateTest extends TestCase
{
    use DatabaseMigrations {
        runDatabaseMigrations as baseRunDatabaseMigrations;
    }
    public function runDatabaseMigrations()
    {
        $this->baseRunDatabaseMigrations();
        $this->artisan('db:seed');
    }
    private $data;

    public function setUp():void{
        parent::setUp();
        echo " => \e[1m Ratest: \e[0m";
    
    $this->data=[
        'code'=>'RAT 1',
        'amount'=>20,
        'market_id'=>1,
        'gyre_id'=>1
    ];
}

    public function testnew(){
        echo "test => \e[32m nuevo\e[0m, ";
        $r = $this->json('POST', '/api/rate',$this->data);
        // dd($r->content());
        $r->assertStatus(201);
        // echo "\e[32m editar\e[0m \n";
        // $this->data['name']="IE 1.1";
        // $id=json_decode($r->content())->id;
        // $response = $this->json('PUT', "/api/school/$id",$this->data);
        // $response->assertStatus(200);

        echo "test => \033[32m actualizar \033[0m \n";
        $this->data['code']='RAT 5';
        $response = $this->json('PUT', '/api/rate/1',$this->data);
        $response->assertStatus(200, "no devolvio 200");
    }

    // public function testupdate(){
        
    // }

    public function testlist(){
        echo "test => \e[32m listar\e[0m \n";
        $r = $this->get('/api/market');
        // dd($r->content());
        $r->assertStatus(200);
    }

}
