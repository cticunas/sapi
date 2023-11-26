<?php

namespace App\Repository;

use App\Models\Category;
use App\Repository\CategoryRepositoryI;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository implements CategoryRepositoryI{
	public function __construct(Category $model){
		parent::__construct($model);
	}
	// $categoriesWithChildren = \App\Models\Category::with('children')->whereNull('parent_id')->get();
    //     echo( $categoriesWithChildren );exit;
    public function listTree($params){
        $orgs_condition = ['status'=>1, 'parent_id'=>null];
        $faculties=DB::table('organizations')->where($orgs_condition)->get();

        $categoriesWithChildren = \App\Models\Category::with('children')->whereNull('parent_id')->get();

        // echo( $categoriesWithChildren );exit;
        foreach ($categoriesWithChildren as $key => $category) {
            foreach ($faculties as $i => $faculty) {
                if ($category->organization_id===$faculty->id) {
                    if (property_exists($faculties[$i], 'children')) {
                        array_push($faculties[$i]->children, $category);
                    } else {
                        $faculties[$i]->children = [$category];
                    }
                    break;
                }
            }
        }
        // echo( $faculties );exit;
        return $faculties;
    }

	// public function listTree($params){
	// 	$data=[];
	// 	$conditions=['status'=>1];
	// 	$orgs_condition = ['status'=>1, 'parent_id'=>null];
	// 	$faculties=DB::table('organizations')->where($orgs_condition)->get();
	// 	$fields = "categories.*";
	// 	if( array_key_exists("inc_counts",$params) ){
	// 		$group_concat_authors = parent::getPDODriver()=='pgsql'?"array_to_string(array_agg(author_id), ',')":"group_concat(author_id)";
	// 		$fields.=", (select $group_concat_authors  from
	// 			(select  distinct(author_id), r.line_id from research_authors oa inner join research r on r.id = oa.research_id where r.status = 1 and (research_state_id=3 or research_state_id=4))t where t.line_id = categories.id) as author_count,
	// 		(select count(research.id) from research where research.line_id = categories.id and research.status = 1 and (research_state_id=3 or research_state_id=4)) as research_count,
	// 		(select count(research.id) from research where research.line_id = categories.id and research.status = 1 and type_research='1' and (research_state_id=3 or research_state_id=4)) as thesis_count,
	// 		(select count(research.id) from research inner join outcomes o on o.research_id = research.id where research.line_id = categories.id and research.status = 1 and o.status=1 and o.type=4 and (research_state_id=3 or research_state_id=4)) as published_count,
	// 		(select count(research.id) from research where research.line_id = categories.id and research.status = 1 and external=1 and (research_state_id=3 or research_state_id=4)) as external_count ";
	// 	}

	// 	$categories=DB::table('categories')
	// 	->selectRaw($fields)
	// 	->where($conditions);
	// 	// exit($categories->toSql());
	// 	$categories=$categories->get()->toArray();
	// 	$tree= array_filter($categories, function($category){ return $category->parent_id==null; });
	// 	$tree= array_values($tree);

	// 	foreach($tree as $i=>$level1){
	// 		$fathers=array_filter($categories, function($category) use ($level1) { return $category->parent_id==$level1->id; } );
	// 		foreach ($fathers as $j => $level2) {
	// 			$children=array_filter($categories, function($category) use ($level2) { return $category->parent_id==$level2->id; } );
	// 			/*foreach ($children as $k => $level3) {
	// 				$grandchildren=array_filter($categories, function($category) use ($level3) { return $category->parent_id==$level3->id; } );
	// 				$children[$k]->children= array_values($grandchildren);
	// 			}*/
	// 			$fathers[$j]->children= array_values($children);
	// 		}
	// 		$tree[$i]->children= array_values($fathers);
	// 	}
	// 	foreach ($faculties as $i => $faculty) {
	// 		$faculties[$i]->children= array_values(array_filter($tree, function($programs) use ($faculty) { return $programs->organization_id==$faculty->id; } )) ;
	// 	}
	// 	return $faculties;
	// }

	public function list_programs_and_lines($params){
		$groups=[];
		$groups_ids=[];
		$conditions=['plan_categories.status'=>1,'plan_id'=>$params['plan_id']];
		$lines=DB::table('plan_categories')
		->join('categories','plan_categories.category_id','=','categories.id')
		->where($conditions)->get();
		foreach ($lines->toArray() as $line) {
			$group=DB::table('categories')->where(['id'=>$line->parent_id])->first();

			//$program=DB::table('categories')->where(['id'=>$group->parent_id])->first();
			$index = array_search($group->id, $groups_ids);
			if ($index === false ) {//no encuentra
				$groups[]=[
					'id'=>$group->id,
					'name'=>$group->name,
					'organization_id'=>$group->organization_id,
					'children'=>[$line]
				];
				$groups_ids[]=$group->id;
			}else{
				$groups[$index]['children'][]=$line;
			}
		}
		return $groups;
	}

	public function get_members($id){
		$conditions=['research.program_id'=>$id,'people.status'=>1];
    $q=DB::table('people')
		->select('people.*')
		->distinct()
		->join('research_authors',"research_authors.author_id",'=','people.id')
		->join('research',"research_authors.research_id",'=','research.id')
		->where($conditions)
		//->orWhere(['research.program_id'=>$id,'people.status'=>1])
		->get();
    return $q;
	}

	public function all($params){
		$conditions=['status'=>1];
		$q=DB::table('categories')->where($conditions);
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
		\App\Models\Category::find($id)->update(['status'=>0]);
	}
}
