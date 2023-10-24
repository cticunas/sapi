<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StandTest extends TestCase
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
        echo " => \e[1m Stands: \e[0m";
    
    $this->data=[
        'code'=>'STAND - 01',
        'location'=>'Ie 1',
        'rate_amount'=>20,
        'adjudication'=>'-',
        'titular_id'=>1,
        'driver_id'=>1,
        'sector_id'=>1,
        'market_id'=>1,
        'gyre_id'=>1,
        'rate_id'=>1,
    ];
}

   
    public function testnew(){
        echo "test => \e[32m nuevo\e[0m, ";
        $r = $this->json('POST', '/api/stand',$this->data);
        $r->assertStatus(201);

        dd($r->content());

        echo "\e[32m editar\e[0m \n";
        $this->data['location']="Mercado 1.1";
        $id=json_decode($r->content())->id;
        $response = $this->json('PUT', "/api/stand/$id",$this->data);
        $response->assertStatus(200);

        echo "test => \033[32m actualizar \033[0m \n";
        $this->data['name']='Mercado 1.1.1';
        $response = $this->json('PUT', '/api/stand/1',$this->data);
        $response->assertStatus(200, "no devolvio 200");
    }

    public function testlist(){
        echo "test => \e[32m listar\e[0m \n";
        $r = $this->get('/api/stand');
        // dd($r->content());
        $r->assertStatus(200);
    }
    
    public function testsearch(){
        echo "test => \e[32m buscar \e[0m \n";
        $r = $this->get('/api/stand?search=fredy');
        // dd($r->content());
        $r->assertStatus(200);
    }
    
    public function testgetaccountstatus(){
        echo "test => \e[32m Listar estatos de cuenta \e[0m \n";
        $r = $this->get('/api/stand/1/account-status/0');
        dd($r->content());
        $r->assertStatus(200);
    }

}
