<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\Models\Debt;
use App\Models\User;
use App\Models\Transaction;
use Faker\Generator as Faker;

class TransactionControllerTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;
    
    private $data;
    private $data_detail;

    public function setUp():void{
        parent::setUp();
        echo " => \e[1m Transactions: \e[0m";

        $faker = new Faker();
        $transactiondetails = [];
        for ($i=0; $i < $faker->numberBetween($min = 1, $max = 10); $i++) { 
            $debt = Debt::query()->inRandomOrder()->first();
            array_push($transactiondetails,[
                'amount' => $i+1,
                'detail' => $debt->code,
                'total'  => $debt->total,
                'debt_id' => $debt->id,
            ]);
        }

        $this->data = [
            // 'code'                  => $faker->ean8,
            'date_at'               => $faker->date($format = 'Y-m-d h:m:s', $max = 'now'),
            'total'                 => array_reduce($transactiondetails, function($carry, $item){
                return $carry += $item['total'];
            },0),
            'client_id'             =>1,
            'transactiondetails'    => $transactiondetails,
            'user_id'               => User::query()->inRandomOrder()->first()->id,
        ];

}
    public function test_index() {
        $response = $this->json('GET','/api/transaction');
        $response->assertSuccessful();
        // $this->assertTrue(json_decode($response->content())->data->total > 0);

    }
   
    public function test_new_transaction(){
        $r = $this->json('POST', '/api/transaction',$this->data);
        $r->assertSuccessful();
        dd($r->content());

        // echo "\e[32m listar\e[0m \n";
        // $r2 = $this->get('/api/transaction/');
        // // dd($r2->content());
        // $listTransaction = json_decode($r2->content());
        // $transaction =  $listTransaction->data[0];
        // unset($transaction->transactiondetails[0]);
        // // convertimos en array los details
        // foreach ($transaction->transactiondetails as $i => $item) {
        //     $transaction->transactiondetails[$i]= (array)$item;
        // }
        
        // echo "\e[32m editar\e[0m \n";
        // $this->data['location']="Mercado 1.1";
        // $id=json_decode($r->content())->id;
        // $response = $this->json('PUT', "/api/transaction/$id",(array)$transaction);
        // // dd($response->content());
        // $response->assertStatus(200);

    }

    public function testlist(){
        echo "test => \e[32m listar\e[0m \n";
        $r = $this->get('/api/transaction');
        // dd($r->content());
        $r->assertStatus(200);
    }
    
    public function testsearch(){
        echo "test => \e[32m buscar \e[0m \n";
        $r = $this->get('/api/transaction?search=fredy');
        // dd($r->content());
        $r->assertStatus(200);
    }

    public function testAllDetailsByConcepts() {
        echo "test => \e[32m listar transacciones detalladas por conceptos \e[0m \n";
        $r = $this->get('/api/transaction/details-by-concepts');
        dd($r->content());
        $r->assertStatus(200);
    }
    
    public function testgetaccountstatus(){
        echo "test => \e[32m Listar estatos de cuenta \e[0m \n";
        $r = $this->get('/api/stand/1/account-status/0');
        // dd($r->content());
        $r->assertStatus(200);
    }

}
