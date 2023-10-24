<?php

namespace App\Repository;

use App\Models\Journal;
use App\Repository\JournalRepositoryI;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\DB; 

class JournalRepository extends BaseRepository implements JournalRepositoryI{
   public function __construct(Journal $model){
	   parent::__construct($model);
   }
   
   public function all($params){
	   $conditions=['status'=>1];
	   $q=DB::table('journals')->where($conditions);
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
		\App\Models\Journal::find($id)->update(['status'=>0]);
	}
}
