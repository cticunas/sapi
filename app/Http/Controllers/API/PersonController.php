<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\PersonRepositoryI;

class PersonController extends Controller
{
    private $repository;
    function __construct( PersonRepositoryI $repository ){
        $this->repository = $repository;
    }

    function index(Request $request){
        $data=$request->all();
        $list=$this->repository->all($data);
        return $list;
    }

    function show($id){
        return $person = $this->repository->find($id);
    }

    function roles(Request $request){
        return $person = $this->repository->list_roles([]);
    }

    function get_author($id){
        return  $this->repository->get_author($id);
    }

    function get_authors(Request $request){
        $data=$request->all();
        $list=$this->repository->get_authors($data);
        return $list;
    }

    function get_author_activity($id){
        return  $this->repository->get_author_activity($id);
    }

    function store(Request $request){
        $data=$request->all();
        $old = null;
        if( array_key_exists('email',$data) && strpos($data['email'],'@') ) 
            $old =$this->repository->getByEmail($data['email']);
        if($old) throw new \Exception("Email duplicado");
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
