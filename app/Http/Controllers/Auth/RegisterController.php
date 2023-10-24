<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/my-school';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $person=\App\Models\Person::create([
            'name'=>$data['name'],
            'lastname'=>$data['lastname'],
            'dni'=>$data['dni'],
            'email'=>$data['email'],
            'sex'=>$data['sex'],
            'ubigeo_id'=>$data['ubigeo_id'],
            'birth'=>$data['birth'],
            'cellphone'=>$data['cellphone'],
        ]);
        $user=User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'person_id'=>$person->id,
            // 'role_id'=>4,
        ]);
        \App\Models\User::find($user->id)->update(['remember_token' => Str::random(10)]);
        //los usuarios que se registran son asesor, por defecto se debe crear school y club
        $director=\App\Models\Person::create(['name'=>'director']);
        $s=\App\Models\Social::create([]);
        $school=\App\Models\School::create(['ubigeo_id'=>1,'director_id'=>$director->id,'social_id'=>$s->id]);
        $s=\App\Models\Social::create([]);
        $club=\App\Models\Club::create(['birth'=>new \Datetime,'school_id'=>$school->id, 'responsable_id'=>$person->id,'social_id'=>$s->id]);
        return $user;
    }
    
}
