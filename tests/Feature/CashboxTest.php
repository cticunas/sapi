<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CashboxTest extends TestCase
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
    private $data_detail;

    public function setUp():void{
        parent::setUp();
        echo " => \e[1m Cashbox: \e[0m";
    
    $this->data=[
        'date_init'=>'2020-06-05 10:10:10',
        'correlative'=>'000004',
        'cashboxdetails'=>[[],[],[]],
    ];

}

   
    public function testOpenCashbox(){
        echo "test => \e[32m nuevo\e[0m, ";
        $r = $this->json('POST', '/api/cashbox',$this->data);
        // dd($r->content());
        $r->assertStatus(201);

        echo "\e[32m listar\e[0m \n";
        $r2 = $this->get('/api/cashbox/');
        // dd($r2->content());
        $listCashbox = json_decode($r2->content());
        $cashbox =  $listCashbox->data[0];
        
        $cashbox->cashboxdetails = [
            ['total' => 200,'concept_id' => 1],
            ['total' => 300,'concept_id' => 2],
        ];

        echo "\e[32m editar\e[0m \n";
        $this->data['date_end']="2020-06-05 19:30:10";
        $id=json_decode($r->content())->id;
        $response = $this->json('PUT', "/api/cashbox/$id",(array)$cashbox);
        // dd($response->content());
        $response->assertStatus(200);

    }

    public function testlist(){
        echo "test => \e[32m listar\e[0m \n";
        $r = $this->get('/api/cashbox');
        // dd($r->content());
        $r->assertStatus(200);
    }
    
}
