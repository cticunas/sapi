<?php

namespace App\Repository;

use App\Models\Event;
use App\Repository\EventRepositoryI;
use Illuminate\Support\Facades\DB;

class EventRepository extends BaseRepository implements EventRepositoryI{
  public function __construct(Event $model){
       parent::__construct($model);
  }
  public function all($params){
     $conditions=['status'=>1];
     $q=DB::table('events')->where($conditions);
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
		\App\Models\Event::find($id)->update(['status'=>0]);
	}
}