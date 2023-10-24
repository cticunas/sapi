<?php

namespace App\Repository;

use App\Models\File;
use \App\Repository\FileRepositoryI;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileRepository extends BaseRepository implements FileRepositoryI{
  public function __construct(\App\Models\File $model){
       parent::__construct($model);
  }
  public function all($params){
    if( array_key_exists('type', $params) ){
      $params["files.type"] = $params["type"];
      unset($params["type"]);
    }
    $conditions=$params;
    $conditions['files.status']=1;
    $q=DB::table('files')
      ->select('files.*',
      DB::raw('people.id as person_id, people.name as person_name, people.lastname as person_lastname'))
      ->join('users','users.id','=','files.user_id')
      ->join('people','users.id','=','people.user_id')
      ->where($conditions);
    return $q->latest()->paginate(10);
  }

   public function save($params){
    if(array_key_exists('id',$params)){
      $o = $this->model->find($params['id']);
      $o->update($params);
    }else{
      $o = $this->model->create($params);
    }
    return $o;
  }

  public function check_and_save($files,$object){
      $new_files = array_filter($files,function($e){ return $e['id']<=0; });
      $old_files = array_filter($files,function($e){ return $e['id']>0; });
      $old_ids = array_map(function($e){ return $e['id']; },$old_files);
      //eliminar a los antiguos.
      $q=$this->model->where(['type'=>$object['type'],'reference_id'=>$object['id']]);
      if(count($old_ids)>0) $q->whereNotIn('id',$old_ids);
      $rows=$q->get();
      foreach($rows as $f){
        $f->update(['status'=>0]);
      }
      //agregar los nuevos.
      $news=[];
      foreach($new_files as $file){
        unset($file['created_at']);
        $data=['name'=>$file['name'],'url'=>$file['url'],'user_id'=>$file['user_id'], 'type'=>$object['type'],'reference_id'=>$object['id']];
        $news[]=$this->model->create($data);
      }
      $new_ids=array_map(function($e){return $e->id;},$news);
      return array_merge($old_ids,$new_ids);
  }

  public function delete($id){
		\App\Models\File::find($id)->update(['status'=>0]);
	}
}