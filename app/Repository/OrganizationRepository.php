<?php

namespace App\Repository;

use App\Models\Organization;
use App\Repository\OrganizationRepositoryI;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\DB; 

class OrganizationRepository extends BaseRepository implements OrganizationRepositoryI{
	public function __construct(Organization $model){
		parent::__construct($model);
	}
	
	public function listTree($params){
		$conditions=['status'=>1];
		$organizations=DB::table('organizations')->where($conditions)->get()->toArray();
		$tree= array_filter($organizations, function($typeOrganization){ return $typeOrganization->parent_id==null; });
		$tree= array_values($tree);

		foreach($tree as $i=>$level1){
			$children=array_filter($organizations, function($typeOrganization) use ($level1) { return $typeOrganization->parent_id==$level1->id; } );
			foreach ($children as $j => $level2) {
				$grandchildren=array_filter($organizations, function($typeOrganization) use ($level2) { return $typeOrganization->parent_id==$level2->id; } );
				$children[$j]->children= array_values($grandchildren);
			}
			$tree[$i]->children= array_values($children);
		}
		return $tree;
	}

	public function all($params){
		$conditions=['status'=>1];
		$q=DB::table('organizations')->where($conditions);
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
		\App\Models\Organization::find($id)->update(['status'=>0]);
	}
}
