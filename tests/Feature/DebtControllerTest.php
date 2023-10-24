<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class DebtControllerTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;

    public function setUp():void{
        parent::setUp();
    }

    public function test_index() {

        $debt = factory(\App\Models\Debt::class,5)->create();

        $response = $this->json('GET','/api/debt');
        dd($response->content());
        $response->assertSuccessful();
        $this->assertTrue(json_decode($response->content())->total > 0);

    }
   

}
