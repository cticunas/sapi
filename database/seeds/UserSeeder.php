<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserSeeder extends Seeder
{
    public function run()
    {


        //martin
        DB::table('users')->insert(
            ['code'=>'1','username'=>'admin','email'=>'martin.pardo@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'martin.pardo@unas.edu.pe'])->update(["user_id"=>1]);
        //OIUNAS
        DB::table('users')->insert(
            ['code'=>'2','username'=>'DGI','email'=>'oiunas@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::create(['name'=>'DGI','lastname'=>'-',"email"=>'oiunas@unas.edu.pe',"user_id"=>2, 'role_id'=>4,'organization_id'=>32,'condition'=>'O']);//escuela Otros

        
        //Info de las Unidades de Investigacion
        DB::table('users')->insert(
            ['code'=>'3','username'=>'UI','email'=>'giannfranco.egoavil@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'giannfranco.egoavil@unas.edu.pe'])->update(["user_id"=>3,'role_id'=>3]);

        DB::table('users')->insert(
            ['code'=>'4','username'=>'UI','email'=>'elizabeth.ordonez@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'elizabeth.ordonez@unas.edu.pe'])->update(["user_id"=>4,'role_id'=>3]);

        DB::table('users')->insert(
            ['code'=>'5','username'=>'UI','email'=>'julian.garcia@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'julian.garcia@unas.edu.pe'])->update(["user_id"=>5,'role_id'=>3]);

        DB::table('users')->insert(
            ['code'=>'6','username'=>'UI','email'=>'segundo.ramirez@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'segundo.ramirez@unas.edu.pe'])->update(["user_id"=>6,'role_id'=>3]);

        DB::table('users')->insert(
            ['code'=>'7','username'=>'UI','email'=>'luis.valdivia@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'luis.valdivia@unas.edu.pe'])->update(["user_id"=>7,'role_id'=>3]);

        DB::table('users')->insert(
            ['code'=>'8','username'=>'UI','email'=>'javier.rodriguez@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'javier.rodriguez@unas.edu.pe'])->update(["user_id"=>8,'role_id'=>3]);

        DB::table('users')->insert(
            ['code'=>'9','username'=>'UI','email'=>'miguel.angulo@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'miguel.angulo@unas.edu.pe'])->update(["user_id"=>9,'role_id'=>3]);

        //--------------------------------------cristian
        DB::table('users')->insert(
            ['code'=>'10','username'=>'admin','email'=>'christian.garcia@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'christian.garcia@unas.edu.pe'])->update(["user_id"=>10, 'role_id'=>1]);
        //loloy Office365
        DB::table('users')->insert(
            ['code'=>'11','username'=>'admin','email'=>'jimmy.loloy@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'jimmy.loloy@unas.edu.pe'])->update(["user_id"=>11, 'role_id'=>1]);
        //brian
        DB::table('users')->insert(
            ['code'=>'12','username'=>'admin','email'=>'brian.pando@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["email"=>'brian.pando@unas.edu.pe'])->update(["user_id"=>12, 'role_id'=>1]);

        DB::table('users')->insert(
            ['code'=>'13','username'=>'admin','email'=>'joseo.castillo@unas.edu.pe','email_verified_at'=>'2020-08-18 10:15:25','password'=>Hash::make('password'),'remember_token'=>Hash::make(microtime())]
        );
        \App\Models\Person::where(["lastname"=>'Castillo Cornelio','name'=>'Jose Orlando'])->update(["user_id"=>13, 'role_id'=>1,'type'=>'D','email'=>'joseo.castillo@unas.edu.pe']);
    }
}