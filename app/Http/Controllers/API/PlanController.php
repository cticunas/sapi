<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\PlanRepositoryI;

class PlanController extends Controller
{
    private $repository;
    function __construct(PlanRepositoryI $repository){
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $data=$request->all();
        $list=$this->repository->all($data);
        return $list;
    }

    public function list_lines(Request $request)
    {
        $data=$request->all();
        $list=$this->repository->list_lines($data);
        return $list;
    }

    public function save_line_actives(Request $request)
    {
        $data=$request->all();
        $o=$this->repository->save_line_actives($data);
        return $o;
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
