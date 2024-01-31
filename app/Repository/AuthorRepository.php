<?php

namespace App\Repository;

use App\Models\ResearchAuthor;
use App\Repository\AuthorRepositoryI;
use Illuminate\Support\Facades\DB;

class AuthorRepository extends BaseRepository implements AuthorRepositoryI{
    public function __construct(ResearchAuthor $model){
        parent::__construct($model);
    }

    public function all($params){
        $conditions=['status'=>1];
        $q=DB::table('research_authors')->where($conditions);
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
		\App\Models\ResearchAuthor::find($id)->update(['status'=>0]);
	}
}
