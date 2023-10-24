<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\MasterRepositoryI;

class MasterController extends Controller
{
    private $repository;
    function __construct( MasterRepositoryI $repository ){
        $this->repository = $repository;
    }

    public function list_states(Request $request){
        $params=$request->all();
        $o=$this->repository->list_states($params);
        return $o;
    }
}
