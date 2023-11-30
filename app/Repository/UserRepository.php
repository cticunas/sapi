<?php

namespace App\Repository;

use App\Models\User;
use App\Repository\UserRepositoryI;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryI{
	public function __construct(User $model){
		parent::__construct($model);
	}

	public function all($params){
		$conditions=['users.status'=>1];
		$conditions=[];
		$q=DB::table('users')
			-> select('users.*',
			DB::raw('people.role_id as role_name, people.name, people.lastname, people.dni'))
			->join("people",'people.user_id',"=","users.id")->where($conditions);
		return $q->orderBy('people.lastname','desc')->paginate(10);
	}

	public function loginByGoogle($params){
		$u=\App\Models\User::where(["email"=>$params['email']])->first();
		if($u){
			if ($u->active==0) throw new \Exception("Usuario desactivado");
			$u->google_id = $params['google_id'];
			$p= \App\Models\Person::where(['user_id'=>$u->id])->first();
			if(! $p->photo ) $p->photo = $params['photo'];
			$p->update();
			$u->update();
		}else{
			$p=\App\Models\Person::where(["email"=>$params['email']])->first();

			$params["role_id"] = 2;
			$u = \App\Models\User::create($params);

			if (is_null($p)) { //Persona existe, pero aun no tiene usuario
				$params['user_id'] = $u->id;
				$p=\App\Models\Person::create($params);
			}
			$p->user_id= $u->id;
			$p->update();

			$u->save();
		}
		$college = false;
		$faculty = false;
		if ($p -> organization_id) {
			$college = \App\Models\Organization::find($p->organization_id);
			$faculty = \App\Models\Organization::find($college -> parent_id);
		}
		//existe asociar al google id del usuario existente
		//si no existe retornar error usuario no encontrado.
		$data=[
			"id"=>$u->id,
			"name"=>$p->name,
			"lastname"=>$p->lastname,
			'username'=>$u->username,
			'email'=>$u->email,
			'role_id'=>$p->role_id,
			'type'=>$p->type,
			'photo'=>$p->photo,
			'people_id'=>$p->id,
			'college_id'=>$college ? $college->id : null,
			'faculty_id'=>$faculty ? $faculty->id : null,
		];
		return $data;
	}

	public function loginByOffice($params){
		$u=\App\Models\User::where(["email"=>$params['email'],'status'=>1])->first();
		if($u){
			if ($u->active==0) throw new \Exception("Usuario desactivado");
			$p= \App\Models\Person::where(['user_id'=>$u->id])->first();
			$u->outlook_id = $params['outlook_id'];
			$u->update();
		}else{
			$p=\App\Models\Person::where(["email"=>$params['email'],'status'=>1])->first();
			$params["role_id"] = 2;
			$u = \App\Models\User::create($params);

			if (is_null($p)) { //Persona existe, pero aun no tiene usuario
				$params['user_id'] = $u->id;
				$p=\App\Models\Person::create($params);
			}
			$p->user_id= $u->id;
			$p->update();

			$u->save();
		}
		$college = false;
		$faculty = false;
		$group=false;
		if (is_null($p)) throw new \Exception("Usuario no registrado correctamente, persona no existe. ");
		if ($p -> organization_id) {
			$college = \App\Models\Organization::find($p->organization_id);
			$faculty = \App\Models\Organization::find($college -> parent_id);
		}
		if($p->group_id){
			$group = \App\Models\Category::find($p->group_id);
		}

		$data=[
			"id"=>$u->id,
			"name"=>$p->name,
			"lastname"=>$p->lastname,
			"photo"=>$p->photo,
			'username'=>$u->username,
			'email'=>$u->email,
			'role_id'=>$p->role_id,
			'people_id'=>$p->id,
			'type'=>$p->type,
			'program_id'=>$p->program_id,
            'area_id'=>$p->area_id,
			'group_id'=>$p->group_id,
			'line_id'=>$p->line_id,
			'college_id'=>$college ? $college->id : null,
			'faculty_id'=>$faculty ? $faculty->id : null,
			'faculty_name'=>$faculty ? $faculty->name : null,
			'group_name'=>$group ? $group->name : null,
		];
		return $data;
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



	public function get($id){
		return $this->model->find($id);
	// return $this->model->with('role')->find($id);
	}

	public function delete($id){
		\App\Models\User::find($id)->update(['status'=>0,'password'=>'--']);
	}
}
