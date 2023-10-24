<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\UserRepositoryI;
use App\Repository\PersonRepositoryI;
use Auth;

class UserController extends Controller
{
    private $repository;
    private $personrepository;
    public function __construct(UserRepositoryI $repository, PersonRepositoryI $personrepository){
       $this->repository = $repository;
       $this->personrepository = $personrepository;
   }
    public function index(Request $request){
        $list=$this->repository->all([]);
        return $list;
    }

    public function loginbygoogle(Request $request){
        $data = $request->all();
        $o = $this->repository->loginByGoogle($data);
        return response()->json($o);
    }

    public function loginbyoffice(Request $request){
        $data = $request->all();
        $o = $this->repository->loginByOffice($data);
        return response()->json($o);
    }

    public function store(Request $request){
        $people_id = $request['people_id'];
        $data=$request->all();
        $data['remember_token']=str_random(60);
        $o = $this->repository->save($data);
        $params_person['id']=$people_id;
        $params_person['user_id']=$o->id;
        $this->personrepository->save($params_person);
        return $o;
    }

    public function show($id){
        return $this->repository->get($id);
    }

    public function update(Request $request, $id)
    {
        $data=$request->all();
        $data['id'] = $id;
        $o = $this->repository->save($data);
        return $o;
    }

    public function destroy($id)
    {
        $o = $this->repository->delete($id);
        return $o;
    }
}
