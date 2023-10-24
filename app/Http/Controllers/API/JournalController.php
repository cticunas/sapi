<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\JournalRepositoryI;

class JournalController extends Controller
{
    private $repository;
    function __construct( JournalRepositoryI $repository ){
        $this->repository = $repository;
    }

    function index(Request $request){
        $list=$this->repository->all([]);
        //$total=100;
        //$data = [ "data"=>$list, 'total'=>$total ];
        return $list;
    }

    function store(Request $request){
        $data=$request->all();
        $o= $this->repository->save($data);
        return $o;
    }

    function update($id, Request $request){
        $data=$request->all();
        $data['id'] = $id;
        $o= $this->repository->save($data);
        return $o;
    }

    function destroy($id){
        $o=$this->repository->delete($id);
        return $o;
    }
}
