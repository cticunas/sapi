<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class StandControllerTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;

    public function setUp():void{
        parent::setUp();
    }

    public function test_index() {

        $stand = factory(\App\Models\Stand::class,5)->create();

        $response = $this->json('GET','/api/stand');
        $response->assertSuccessful();
        $this->assertTrue(json_decode($response->content())->total > 0);

    }
   
    public function test_create_new_stand(){

        $stand=[
            'code'=>'STAND - 01',
            'location'=>'Ie 1',
            'rate_amount'=>20,
            'adjudication'=>'-',
            'titular_id'=>1,
            'sector_id'=>1,
            'market_id'=>1,
            'gyre_id'=>1,
            'rate_id'=>1,
        ];

        $response = $this->json('POST', '/api/stand',$stand);
        $response->assertSuccessful();
        $this->assertDatabaseHas('stands',$stand);

    }

    public function test_updated_stand(){
        $stand = factory(\App\Models\Stand::class)->create();
        $data = [
            'code' => 'Update code stand',
        ];

        $response = $this->json('put',"/api/stand/{$stand->getKey()}",$data);
        $response->assertSuccessful();
        $this->assertDatabaseHas('stands', [
            'code' => 'Update code stand',
        ]);
    }

    public function test_show_stand() {
        $stand = factory(\App\Models\Stand::class)->create();

        $response = $this->json('get',"/api/stand/{$stand->getKey()}");
        $response->assertSuccessful();
    }

    public function test_delete_stand() {
        $stand = factory(\App\Models\Stand::class)->create();
        $data = [
            'status' => 0,
        ];

        $response = $this->json('put',"/api/stand/{$stand->getKey()}",$data);
        $response->assertSuccessful();
        $this->assertDatabaseHas('stands', [
            'status' => 0,
        ]);
    }

    public function test_search_stand(){
        $stand = factory(\App\Models\Stand::class,5)->create();

        $response = $this->get('/api/stand?search=fredy');
        $response->assertSuccessful();
    }

    /**
     * Start Group Test
     * About: getAccountStatusByMonth
    **/
    public function test_get_account_status_of_stand_by_month_and_all_filter(){
        $response = $this->get('/api/account-status-by-month?stand_id=1&concept_id=1&sector_id=1&type=normal&payment_status=0');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_stand_by_month_and_none_filter(){
        $response = $this->get('/api/account-status-by-month');
        $response->assertSuccessful();
    }

    /**
     * Finish Group Test
    **/

}
