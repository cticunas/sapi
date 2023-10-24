<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\AuthorRepositoryI;

class AuthorController extends Controller
{
    private $repository;
    function __construct(AuthorRepositoryI $repository){
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $params=$request->all();
        $tree=$this->repository->listTree($params);
        return $tree;
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $o=$this->repository->save($data);
        return $o;
    }

    public function update($id, Request $request)
    {
        $data=$request->all();
        $data['id'] = $id;
        $o=$this->repository->save($data);
        return $o;
    }

    public function destroy($id)
    {
        $o=$this->repository->delete($id);
        return $o;
    }
}
