<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
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
        echo " => \e[1m User: \e[0m";
    
    $this->data=[
        'name'=>'admin',
        'email'=>'admintest@gmail.com',
        'email_verified_at'=>'2020-08-18 10:15:20',
        'password'=>'admin123',
        'code'=>'123',
    ];
}

   
    public function testnew(){
        echo "test => \e[32m nuevo\e[0m, ";
        $r = $this->json('POST', '/api/user',$this->data);
        // dd($r->content);
        $r->assertStatus(201);

    }

    public function testedit() {
        echo "\e[32m editar\e[0m \n";
        $this->data['email']="superadmintest@gmail.com";
        $response = $this->json('PUT', "/api/user/1",$this->data);
        $response->assertStatus(200);
    }

    public function testlist(){
        echo "test => \e[32m listar\e[0m \n";
        $r = $this->get('/api/user');
        // dd($r->content());
        $r->assertStatus(200);
    }
    
    public function testvalidatecode(){
        echo "test => \e[32m validar codigo usuario \e[0m \n";
        $r = $this->get('/api/user/1/validate-code/1234');
        // dd($r->content());
        $r->assertStatus(200);
    }
    

}
