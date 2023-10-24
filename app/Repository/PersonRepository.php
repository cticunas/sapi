<?php

namespace App\Repository;

use App\Models\Person;
use App\Repository\PersonRepositoryI;
use Illuminate\Support\Facades\DB; 

class PersonRepository extends BaseRepository implements PersonRepositoryI{
  public function __construct(Person $model){
    parent::__construct($model);
  }

  public function all($params){
    if( array_key_exists('all_status',$params) )$conditions=[];
		else $conditions=['people.status'=>1];
    //echo $params['search'];
    $p =DB::table('people')
    ->select('people.*',
    DB::raw("CONCAT(people.lastname, ' ', people.name) AS fullname, roles.name AS role, organizations.name AS organization"));
		$p->join('roles','roles.id','=','people.role_id');
    $p->leftJoin('users', 'users.id', '=', 'people.user_id');
    $p->leftJoin('organizations', 'organizations.id', '=', 'people.organization_id');
    $p -> where($conditions);
    if( array_key_exists("search",$params) ){ 	
      //$p->whereRaw("(CONCAT(people.name, ' ', people.lastname) like '%$params[search]%' OR CONCAT(people.lastname, ' ', people.name) like '%$params[search]%'  OR CONCAT( roles.name, people.email ) like '%$params[search]%')");}
      $p->whereRaw("(LOWER(CONCAT(people.name,' ',people.lastname)) like LOWER('%$params[search]%') OR LOWER(CONCAT(people.lastname,' ',people.name)) like LOWER('%$params[search]%') OR CONCAT(roles.name,people.email) like LOWER('%$params[search]%'))");}
    if( array_key_exists("sql",$params) ) exit($p->toSql());
    return $p->orderBy('lastname', 'ASC')->orderBy('id', 'DESC')->paginate(10);
   }

  public function get_photo($id){
    $conditions=["people.id" => $id];
    $q =DB::table('people')
    -> select('photo')
    -> join('users', 'users.id', '=', 'people.user_id')
    -> where($conditions);
    return $q->get();
  }

  public function list_roles($params){
    $conditions=[];
    $q =DB::table('roles')
    -> select('roles.*')
    -> where($conditions);
    return $q->get();
  }

  public function get_author($id){
    $conditions=['people.id'=>$id];
    $q =DB::table('people')
    -> select('people.*')
    ->where($conditions );
    $author=$q->first();

    $lines_list=DB::table('categories')
        ->select('categories.*')
        ->join('research','categories.id','=','research.line_id')
        ->join('research_authors','research.id','=','research_authors.research_id')
        ->where(['research_authors.author_id'=>$id])
        ->whereNotIn('categories.name',['No definido'])
        ->distinct()->get();

    $research_list=DB::table('research')->selectRaw('research.id, date_init, date_end, title, research_states.name as state')
    ->join('research_authors',"research.id",'=','research_authors.research_id')
    ->join('research_states','research.research_state_id','=','research_states.id')
    ->where(['research_authors.author_id'=>$id,'research_authors.status'=>1])->orderBy('date_init','DESC')->get();
    $data=['author'=>$author,'research'=>$research_list,'lines'=>$lines_list];
    return $data;
  }

  public function get_author_activity($id){
    $conditions3=['research_authors.author_id'=>$id,'research.status'=>1];
    $year_date_init = parent::getYearSqlPart("research.date_init");
    $q3 =DB::table('research')
    -> select(DB::raw("$year_date_init as year,count(research_authors.id) as q"))
    ->join('research_authors',"research_authors.research_id", '=','research.id')
    ->where($conditions3)->groupBy(DB::raw("$year_date_init "));
    return $q3->get();
  }

  public function get_authors($params){
    $xpage = array_key_exists('xpage',$params)? $params['xpage']:10;
    $conditions=['people.status'=>1,'research_authors.status'=>1,'research.status'=>1];
    $q=DB::table('research_authors')
    ->selectRaw('people.*, (select count(id) from research_authors ra where ra.id = people.id) as research_count')
    ->distinct()
    ->join('research','research.id','=',"research_authors.research_id")
    ->join('people',"people.id",'=','research_authors.author_id')
    ->where($conditions)
    ->whereRaw("(research_state_id='3' or research_state_id='4')");
    if( array_key_exists("search",$params) ){ $q->whereRaw("LOWER(CONCAT(people.name, ' ', people.lastname, people.email)) like LOWER('%$params[search]%') ");}
    if( array_key_exists("program_id",$params) ){ $q->whereRaw("research.program_id = '$params[program_id]'");}
    if( array_key_exists("group_id",$params) ){ $q->whereRaw("research.group_id = '$params[group_id]'");}
    if( array_key_exists("line_id",$params) ){ $q->whereRaw("research.line_id = '$params[line_id]'");}
    $q->orderByRaw( "(select count(id) from research_authors ra where ra.id = people.id) desc" );
    //exit($q->toSql());
    return $q->paginate($xpage);
  }

  public function save($params){
    if( array_key_exists('dni',$params) && empty($params['dni']) ) $params['dni']='';
    if( array_key_exists('other_degree',$params) && empty($params['other_degree']) ) $params['other_degree']='';
     $photo=\App\Util::verifyPhoto($params);
    unset($params['photo']); 
    if(array_key_exists('id',$params)){
      $o = $this->model->find($params['id']);
      if (($o->email != $params['email']) && ($o->user_id != null)) {
        \App\Models\User::find($o->user_id)->update(['email' => $params['email'], 'google_id' => null, 'outlook_id' => null]);
      }
      $o->update($params);
    }else{
        $o = $this->model->create($params);
    }
    
    if(!empty($photo) && \App\Util::is_base64image($photo) ){
      $new_photo = asset( \App\Util::storageImage("person_$o[id]",$photo) );
      
      $o->update(['photo'=>$new_photo]);
      \App\Models\User::find($o->user_id)->update(['photo'=>$new_photo]);
    }
    return $o;
  }

  public function getByEmail($email){
		return 	\App\Models\Person::where(["email"=>$email,'status'=>1])->first();
	}

  public function verifyDuplicateDNI($params){
    return \App\Models\Person::where("dni",$params['dni'])->first();
  }

  public function delete($id){
    $p = \App\Models\Person::find($id); 
    $u = \App\Models\User::find($p->user_id);
		$p->update(['status'=>0]);
    if($u) $u->update(['active'=>0,'status'=>0]);
	}
}