<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function greetings(){
        return "Hello";
    }

    public function index(Request $request){
        return view('home');
    }

    public function public(Request $request){
        $tutos = \App\Models\File::where('reference_id',2)->get();
        $docs = \App\Models\File::where('reference_id',1)->get();

        return view('public',['tutos'=> $tutos, 'docs'=> $docs ]);
     }

}