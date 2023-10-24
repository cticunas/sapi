<?php

namespace App\Repository;

use App\Models\Document;
use App\Repository\DocumentRepositoryI;
use Illuminate\Support\Facades\DB;

class DocumentRepository extends BaseRepository implements DocumentRepositoryI{
  public function __construct(Document $model){
       parent::__construct($model);
  }
  public function all($params){
     $conditions=['status'=>1];
     $q=DB::table('documents')->where($conditions);
     return $q->latest()->paginate(10);
  }

   public function save($params){
    if( array_key_exists("files",$params) ){
			$files = $params['files'];
			unset($params['files']);
		}
    if(array_key_exists('id',$params)){
      $o = $this->model->find($params['id']);
      $o->update($params);
    }else{
      $o = $this->model->create($params);
    }
    return $o;
  }

  public function delete($id){
		\App\Models\File::find($id)->update(['status'=>0]);
	}
}