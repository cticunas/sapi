<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FractionaryTest extends TestCase
{
    // use DatabaseMigrations {
    //     runDatabaseMigrations as baseRunDatabaseMigrations;
    // }
    // public function runDatabaseMigrations()
    // {
    //     $this->baseRunDatabaseMigrations();
    //     $this->artisan('db:seed');
    // }
    private $user_token;
    private $frac_merced;
    private $frac_sisa;
    private $frac_kiosko;
    private $frac_pit;
    private $frac_terr;
    private $frac_mau;
    private $frac_pnl;

    public function setUp():void{
        parent::setUp();
        $user = \App\Models\User::find(1);
        $this->user_token = md5($user->remember_token);
        echo " => \e[1m Fractionary: \e[0m";
    
    $this->frac_merced=[
        "fractionary_type_id"=>1,
        "titular_id"=>1,
        "stand_id"=>1,
        "concept_id"=>1,
        "representative_id"=>4,
        "reference_id"=>"400,401,402",
        "reference_description"=>"D-MC-20200002, D-MC-20200003, D-MC-2020003",
        "quotes"=>"10",
        "date_at"=>"2020-08-26T16:18:08.605Z",
        "date_start"=>"2020-08-26T16:18:08.605Z",
        "interest"=>"0.08",
        "debt"=>"1000",
        "initial_voucher"=>"123123",
        "initial_amount"=>"123",
        "fractionary_amount"=>"800",
        "user_id"=>"1",
        "schedule"=>[["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],],
    ];

    $this->frac_sisa=[
        "fractionary_type_id"=>2,
        "titular_id"=>1,
        "concept_id"=>1,
        "representative_id"=>4,
        "reference_id"=>"1,2,3",
        "reference_description"=>"D-MC-201902, D-MC-201902, D-MC-201903",
        "quotes"=>"10",
        "date_at"=>"2020-08-26T16:18:08.605Z",
        "date_start"=>"2020-08-26T16:18:08.605Z",
        "period_init"=>"2020-08-26T16:18:08.605Z",
        "period_finish"=>"2020-08-26T16:18:08.605Z",
        "interest"=>"0.08",
        "debt"=>"1000",
        "initial_voucher"=>"123123",
        "initial_amount"=>"123",
        "fractionary_amount"=>"800",
        "user_id"=>"1",
        "schedule"=>[["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],],
    ];

    $this->frac_kiosko=[
        "fractionary_type_id"=>3,
        "titular_id"=>1,
        "concept_id"=>1,
        "representative_id"=>4,
        "reference_id"=>"400,401,402",
        "reference_description"=>"D-MC-20200002, D-MC-20200003, D-MC-2020003",
        "quotes"=>"10",
        "date_at"=>"2020-08-26T16:18:08.605Z",
        "date_start"=>"2020-08-26T16:18:08.605Z",
        "period_init"=>"2020-08-26T16:18:08.605Z",
        "period_finish"=>"2020-08-26T16:18:08.605Z",
        "interest"=>"0.08",
        "debt"=>"1000",
        "initial_voucher"=>"123123",
        "initial_amount"=>"123",
        "fractionary_amount"=>"800",
        "user_id"=>"1",
        "schedule"=>[["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],],
    ];

    $this->frac_pit=[
        "fractionary_type_id"=>4,
        "titular_id"=>1,
        "concept_id"=>1,
        "representative_id"=>4,
        "quotes"=>"10",
        "date_at"=>"2020-08-26T16:18:08.605Z",
        "date_start"=>"2020-08-26T16:18:08.605Z",
        "period_init"=>"2020-08-26T16:18:08.605Z",
        "period_finish"=>"2020-08-26T16:18:08.605Z",
        "interest"=>"0.08",
        "debt"=>"1000",
        "initial_voucher"=>"123123",
        "initial_amount"=>"123",
        "fractionary_amount"=>"800",
        "user_id"=>"1",
        "schedule"=>[["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],],
    ];

    $this->frac_terr=[
        "fractionary_type_id"=>5,
        "titular_id"=>1,
        "concept_id"=>1,
        "representative_id"=>4,
        "quotes"=>"10",
        "date_at"=>"2020-08-26T16:18:08.605Z",
        "date_start"=>"2020-08-26T16:18:08.605Z",
        "period_init"=>"2020-08-26T16:18:08.605Z",
        "period_finish"=>"2020-08-26T16:18:08.605Z",
        "interest"=>"0.08",
        "debt"=>"1000",
        "initial_voucher"=>"123123",
        "initial_amount"=>"123",
        "fractionary_amount"=>"800",
        "user_id"=>"1",
        "schedule"=>[["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],],
    ];

    $this->frac_pnl=[
        "fractionary_type_id"=>6,
        "titular_id"=>1,
        "concept_id"=>1,
        "representative_id"=>4,
        "quotes"=>"10",
        "date_at"=>"2020-08-26T16:18:08.605Z",
        "date_start"=>"2020-08-26T16:18:08.605Z",
        "period_init"=>"2020-08-26T16:18:08.605Z",
        "period_finish"=>"2020-08-26T16:18:08.605Z",
        "interest"=>"0.08",
        "debt"=>"1000",
        "initial_voucher"=>"123123",
        "initial_amount"=>"123",
        "fractionary_amount"=>"800",
        "user_id"=>"1",
        "schedule"=>[["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],["schedule_date_at"=>"2020-08-26T16:18:08.605Z","pending_amount"=>3150,"interest"=>27.32,"amort"=>350,"total"=>377.72,"status"=>1],],
    ];

}
    public function testnew_merced(){
        echo "test => \e[32m nuevo MercedCondutiva\e[0m, ";
        $r = $this->withHeaders(['Authorization' => $this->user_token])->json('POST', '/api/fractionary',$this->frac_merced);
         dd($r->content());
        $r->assertStatus(201);
    }

    // public function testlist(){
    //     echo "test => \e[32m listar\e[0m \n";
    //     $r = $this->get('/api/fractionary');
    //     dd($r->content());
    //     $r->assertStatus(200);
    // }
   
    

}
