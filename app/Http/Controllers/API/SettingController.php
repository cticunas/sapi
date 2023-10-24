<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\SettingRepositoryI;

class SettingController extends Controller
{
    private $repository;
    public function __construct(SettingRepositoryI $repository){
        $this->repository = $repository;
     }

     public function index(Request $request){
        $params = $request->query;
        $params=[];
        $l = $this->repository->all($params);
        return response()->json($l);
    }

    public function store(Request $request){
        $data=$request->all();
        $o = $this->repository->save($data);
        return response()->json($o);
    }

  
    public function show($id){
        return $this->repository->get($id);
    }

    public function update(Request $request, $id){
        $data=$request->all();
        $data['id'] = $id;
        $o = $this->repository->save($data);
        return response()->json($o);
        
    }

    public function destroy($id){
        //
    }
}
