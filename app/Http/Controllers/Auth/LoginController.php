<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use App\Http\Controllers\Auth\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/home';
    protected function authenticated($request, $user){
        $home="/";
        // switch ($user->role->name ) {
        //     case 'asesor': $home="/collection"; break;
        //     case 'dre': $home="/my-dre"; break;
        //     case 'ugel': $home="/my-ugel"; break;
        // }

        return redirect($home);
    } 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    // protected function credentials($request){
    //     return array_merge($request->only($this->username(), 'password'), ['status' => 1]);
    // }

    public function login(Request $request){   
        $input = $request->all();
        $this->validate($request, ['username' => 'required','password' => 'required',]);
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        if(auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password']))){
            //return redirect()->route('home');
            //$u=\App\Models\User::find( \Auth::user()->id );
            //$u->update(['remember_token'=>Hash::make(\Auth::user()->username.microtime())]);
            return redirect("/");
        }else{
            return redirect()->route('login')->with('error','Email-Address And Password Are Wrong.');

        }

          

    }
}
