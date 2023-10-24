<?php

namespace App\Repository;

use App\Models\Renacyt;
use App\Repository\RenacytRepositoryI;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\DB; 

class RenacytRepository extends BaseRepository implements RenacytRepositoryI{
   public function __construct(Renacyt $model){
	   parent::__construct($model);
   }
   
   public function all($params){
	   $conditions=['status'=>1];
	   $q=DB::table('renacyts')->where($conditions);
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
	
	public function delete($id){
		\App\Models\Renacyt::find($id)->update(['status'=>0]);
	}
}
