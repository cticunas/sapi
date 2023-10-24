<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\FileRepositoryI;

class FileController extends Controller
{
    private $repository;
    public function __construct(FileRepositoryI $repository ){
       $this->repository = $repository;
    }
    public function index(Request $request){
        $params = [];
        if($request->query('type')) $params['type']=$request->query('type');
        if($request->query('reference_id')) $params['reference_id']=$request->query('reference_id');
        $l = $this->repository->all($params);
        return response()->json($l);
    }
    public function uploadtemp(Request $request){
        $file = $request->file('file'); 
        // dd("ok");
    }
    public function uploadfile(Request $request){
        $file = $request->file('file'); 
        $file->getClientOriginalName();
        $file->getClientOriginalExtension();
        $file->getRealPath();
        $file->getSize();
        $file->getMimeType();//application/pdf
        //$destinationPath = 'uploads';
        //$file->move($destinationPath,$file->getClientOriginalName());
        $filename= str_replace(".",strtotime('now').".",$file->getClientOriginalName() );
        $url=\App\Util::storageFile($filename,file_get_contents($file->getRealPath()));
        return response()->json([
            'name'=>$file->getClientOriginalName(),
            'status'=>'done',
            'url'=>$url,
            "thumbUrl"=>$url
        ] );
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
