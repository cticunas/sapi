<?php

namespace App\Repository;

use App\Repository\MasterRepositoryI;

class MasterRepository extends BaseRepository implements MasterRepositoryI{
   public function __construct( ){  }

	public function list_states($params){
		return \App\Models\ResearchStates::get();
	}
}
