<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\CategoryRepositoryI;

class CategoryController extends Controller
{
    private $repository;
    function __construct(CategoryRepositoryI $repository){
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $params=$request->all();
        $tree=$this->repository->listTree($params);
        return $tree;
    }

    public function number_of_investigations(Request $request)
    {
        $params=$request->all();
        $list=$this->repository->number_of_investigations($params);
        return $list;
    }

    public function list_programs_and_lines(Request $request)
    {
        $params=$request->all();
        $list=$this->repository->list_programs_and_lines($params);
        return $list;
    }

    public function get_members($id){
        return $this->repository->get_members($id);
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
