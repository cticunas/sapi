<?php

namespace App\Repository;

use App\Models\Plan;
use App\Models\PlanCategories;
use App\Repository\PlanRepositoryI;
use Illuminate\Support\Facades\DB; 

class PlanRepository extends BaseRepository implements PlanRepositoryI{
   public function __construct(Plan $model){
	   parent::__construct($model);
   }
   
   public function all($params){
	   $conditions=['status'=>1];
		 $xpage = array_key_exists('xpage', $params)?$params['xpage']:10;
		 if (array_key_exists('active', $params)) {
				$conditions['active'] = $params['active'];
		 }
	   $q=DB::table('plans')
		 ->where($conditions);
	   return $q->orderBy('plans.id', 'desc')->paginate($xpage);
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

	public function list_lines($params){
		$conditions=['status'=>1,'plan_id'=>$params['plan_id']];
		$q=DB::table('plan_categories')
		->where($conditions);
		return $q->get();
	}

	public function save_line_actives($params){
		$pcs = \App\Models\PlanCategories::where('plan_id',$params['plan_id'])->get();

		$o = \App\Models\PlanCategories::where('plan_id',$params['plan_id'])->update(['status'=>0]);
		foreach($params['lines'] as $line){
			if (in_array($line,$pcs->toArray())) {
				$o = \App\Models\PlanCategories::where('plan_id',$params['plan_id'])->where('category_id',$line)->update(['status'=>1]);
			}else{
				$o = \App\Models\PlanCategories::create(['plan_id'=>$params['plan_id'],'category_id'=>$line]);
			}
		}
		return $o;
	}
	
	public function delete($id){
		\App\Models\Plan::find($id)->update(['status'=>0]);
	}
}
