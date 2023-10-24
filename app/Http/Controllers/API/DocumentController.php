<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\DocumentRepositoryI;

class DocumentController extends Controller
{
    private $repository;
    public function __construct(DocumentRepositoryI $repository ){
       $this->repository = $repository;
    }
    public function index(Request $request){
        $params = $request->all();
        $params['status'] = 1;
        return $this->repository->all($params);
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
        return $this->repository->delete($id);
    }
}
