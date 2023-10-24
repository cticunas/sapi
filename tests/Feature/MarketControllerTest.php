<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class MarketControllerTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;

    public function setUp():void{
        parent::setUp();
    }

    public function test_index() {
        $stand = factory(\App\Models\Market::class,5)->create();

        $response = $this->json('GET','/api/market');
        $response->assertSuccessful();
        $this->assertTrue(json_decode($response->content())->total > 0);

    }
   
    public function test_create_new_market(){
        $market=[
            'code'           => 'ABT005',
            'name'           => 'Mercado Alas',
            'type'           => 'merced',
            'address'        => 'Av. Alameda',
            'photo'          => 'img/animal/market.jpg',
        ];

        $response = $this->json('POST', '/api/market',$market);
        $response->assertSuccessful();
        // $this->assertDatabaseHas('markets',$market);

    }

    public function test_updated_market(){
        $stand = factory(\App\Models\Market::class)->create();
        $data = [
            'code' => 'Update code market',
        ];

        $response = $this->json('put',"/api/market/{$stand->getKey()}",$data);
        $response->assertSuccessful();
        // $this->assertDatabaseHas('markets', [
        //     'code' => 'Update code stand',
        // ]);
    }

    public function test_show_market() {
        $market = factory(\App\Models\Market::class)->create();

        $response = $this->json('get',"/api/market/{$market->getKey()}");
        $response->assertSuccessful();
    }

    public function test_delete_market() {
        $market = factory(\App\Models\Market::class)->create();
        $data = [
            'status' => 0,
        ];

        $response = $this->json('put',"/api/market/{$market->getKey()}",$data);
        $response->assertSuccessful();
        // $this->assertDatabaseHas('markets', [
        //     'status' => 0,
        // ]);
    }

    public function test_search_market(){
        $market = factory(\App\Models\Market::class,5)->create();

        $response = $this->json('GET','/api/market?search=fredy');
        $response->assertSuccessful();
    }

    /**
     * Start Group Test
     * About: getAccountStatusByStands
    **/
    
    public function test_get_account_status_of_market_by_stands_and_all_filter(){
        $response = $this->json('GET','/api/market/account-status-by-stands?market_id=1?concept_id=1&sector_id=1&payment_status=0&type=normal');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_market_by_stands_and_none_filter(){
        $response = $this->json('GET','/api/market/account-status-by-stands');
        $response->assertSuccessful();
    }

    /**
     * Finish Group Test
    **/

    /**
     * Start Group Test
     * About: getAccountStatusByYear
    **/
    
    public function test_get_account_status_of_market_by_year_and_all_filter(){
        $response = $this->json('GET','/api/market/account-status-by-year?concept_id=1&sector_id=1&payment_status=0&type=normal');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_market_by_year_and_none_filter(){
        $response = $this->json('GET','/api/market/account-status-by-year');
        $response->assertSuccessful();
    }

    /**
     * Finish Group Test
    **/
    
    /**
     * Start Group Test
     * About: getAccountStatusByMonth
    **/
    public function test_get_account_status_of_market_by_month_and_all_filter() {
        $response = $this->json('GET','/api/market/account-status-by-month?market_id=1&concept_id=1&sector_id=1&payment_status=0&type=normal&year=2020');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_market_by_month_and_none_filter() {
        $response = $this->json('GET','/api/market/account-status-by-month');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_market_by_month_and_market_filter() {
        $response = $this->json('GET','/api/market/account-status-by-month?market_id=1');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_market_by_month_and_market_and_paymentStatus_filter() {
        $response = $this->json('GET','/api/market/account-status-by-month?market_id=1&payment_status=0');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_market_by_month_and_market_and_type_filter() {
        $response = $this->json('GET','/api/market/account-status-by-month?market_id=1&type=normal');
        $response->assertSuccessful();
    }

    public function test_get_account_status_of_market_by_month_and_paymentStatus_and_type_filter() {
        $response = $this->json('GET','/api/market/account-status-by-month?payment_status=0&type=normal');
        $response->assertSuccessful();
    }

    /**
     * Finish Group Test
    **/

}
