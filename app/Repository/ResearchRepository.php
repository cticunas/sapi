<?php

namespace App\Repository;

use App\Models\Research;
use App\Repository\ResearchRepositoryI;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResearchRepository extends BaseRepository implements ResearchRepositoryI{

    public function __construct(Research $model){
        parent::__construct($model);
    }

	private $r_states=['-','No def','Nuevo','En Ejecución', 'Culminado', 'Suspendido','Anulado'];
	private $r_types=[ '-','Tesis','Inv. Docente','Experiencia Profesional', 'Proyecto Innovacion'];
	private $grades=[ '-','Pregrado','Posgrado'];

  public function all($params){
		$sql_authors = parent::getResearchAuthorsSqlPart();
    $conditions=['research.status'=>1];
		$date_init_f = parent::getDateFormatSql('research.date_init', 'd/m/Y');
		$date_end_f = parent::getDateFormatSql('research.date_end', 'd/m/Y');
    $q=DB::table('research')
		-> select('research.*',
		//DB::raw("$date_init_f as date_init,  TIMESTAMPDIFF(MONTH, date_end, research.date_init) as spenttime, 	$sql_authors  as authors"));
		DB::raw("$date_init_f as date_init, $date_end_f as date_end,	$sql_authors  as authors"));

		if (array_key_exists("type_research", $params)) {
			$conditions[]=['research.type_research','=', $params['type_research']];
		}
		if( array_key_exists("author_id",$params) ){
			$q -> join('research_authors', 'research_id','=','research.id');
			$conditions[]=['author_id','=', $params['author_id']];
			$conditions[]=['research_authors.status','=', '1'];
		}
		if( array_key_exists("faculty_id",$params) ){
			$q->join('organizations', "organizations.id", '=', 'research.organization_id');
			$conditions[]=['organizations.parent_id','=', $params['faculty_id']];
		}
		if( array_key_exists("group_id",$params) ){
			$q->join('categories', "categories.id", '=', 'research.line_id');
			$conditions[]=['categories.parent_id','=', $params['group_id']];
		}
		if( array_key_exists("organization_id",$params) ){
				$q->join('organizations', "organizations.id", '=', 'research.organization_id');
				$conditions[]=['organization_id','=', $params['organization_id']];
		}
		if (array_key_exists('own_research', $params)) {
			$conditions[]=['own_research','=', $params['own_research']];
		}
		if (array_key_exists('external', $params)) {
			$conditions[]=['external','=', $params['external']];
		}
		if (array_key_exists('incentive', $params)) {
			$conditions[]=['incentive','=', $params['incentive']];
		}
		$q -> where($conditions);
		if( array_key_exists("text",$params) ){
			$q->whereRaw("(LOWER(CONCAT(research.title)) like LOWER('%$params[text]%') OR LOWER($sql_authors) like LOWER('%$params[text]%'))");
		}
		if (array_key_exists("from", $params)) {
			$q->whereRaw("( research.date_init between '$params[from]' and '$params[to]' or research.date_end between '$params[from]' and '$params[to]' )   ");
		}
		if (array_key_exists("state", $params)) {
			$q->whereRaw("research.research_state_id = '$params[state]' ");
		}

		if (array_key_exists("in_finish", $params)) {// inv. de estado no culminadas
			// $now = date('Y-m-d');
			$sqlHasFinalInform = "(select count(o.id) from outcomes o where o.status=1 and o.type=3 and o.research_id=research.id ) > 0 AND research.research_state_id != 4 ";
			$sqlHasPaper = "(select count(o.id) from outcomes o where o.status=1 and o.type=4 and o.research_id=research.id ) > 0 AND research.research_state_id != 4";
			// $sqlHasPaper = "(select count(o.id) from outcomes o where o.status=1 and o.type=4 and o.research_id=research.id ) > 0 and research.date_end < '$now' AND research.research_state_id != 4";
			$q->whereRaw(" ($sqlHasPaper) ");
		}
		if (array_key_exists("in_work", $params)) {// inv. de estado no culminadas
			$sqlHasOutcome = "(select count(o.id) from outcomes o where o.status=1 and o.research_id=research.id ) > 0 AND research.research_state_id = 2 ";
			$q->whereRaw("  ($sqlHasOutcome) ");
		}
		//echo $q->toSql(); exit;
		return $q->orderBy('research.date_init', 'desc')->orderBy('research.id', 'desc')->paginate(10);
	}


	public function explode_authors($authors_string , $separate_name=true, $inc_photo=false, $inc_type=false, $inc_condition=false, $inc_dni=false){
		$authors=[];
		$r_authors = [ 'TI'=>'Titular', 'AS'=>'Asesor','OR'=>'M. Ordinario', 'EO'=>'Extraord.', 'IC'=>'Inic. Cientif.','CO'=>'Consultor', 'OT'=>'Otros' ];
		if( strpos($authors_string,'|')>0 ){
			$authors_ = explode(",",$authors_string);
			//dd($authors_);
		foreach($authors_ as $a){
			$i=0;
			$a_ = explode("|",$a);
			//dd($a_);
				$author = ["id"=>$a_[$i++], "name"=>$a_[$i++]];
				if($separate_name) $author['lastname']= $a_[$i++];
				$author["role"]=$r_authors[$a_[$i++]];
				//dd($a_);
				if($inc_photo) $author['photo']= $a_[$i++];
				if( $inc_type ) $author['type']= $a_[$i++];
				if( $inc_condition ) $author['condition']= $a_[$i++];
				if( $inc_dni ) $author['dni']= $a_[$i++];
				//dd($author);
				$authors[] =	$author ;
		}
		}

		return $authors;
	}

    public function public_list_new($params) {
        // dd($params);
        $sql_authors = parent::getOutcomeAuthorsSqlPart();

        $outcome_types = [];
        if (array_key_exists("type", $params)) {
            // $params['type'] desde el front viene con el valor 4
            $conditions = [['research.status', '=', 1], ['outcomes.status', '=', 1], ['outcomes.type', '=', $params['type']]];
        } else {
            $outcome_types = [1, 4]; // 1: Proyecto, 4: Articulo
            $conditions = [['research.status', '=', 1], ['outcomes.status', '=', 1]];
        }
        $sql_research_authors = parent::getResearchAuthorsSqlPart();
        $fields = "outcomes.id,outcomes.type,outcomes.name,outcomes.date,outcomes.url,outcomes.doi,outcomes.journal, $sql_authors as authors, indexed";

        if (array_key_exists("external", $params)) $conditions[] = parent::add_condition('external', $params);
        if (array_key_exists("type_research", $params)) $conditions[] = parent::add_condition('type_research', $params);

        $q = DB::table('outcomes')
            ->select(DB::raw($fields))
            ->join('research', "research.id", '=', 'outcomes.research_id');

        if (array_key_exists("line_id", $params)) $conditions[] = parent::add_condition('line_id', $params);
        if (array_key_exists("group_id", $params)) $conditions[] = parent::add_condition('group_id', $params);
		if (array_key_exists("area_id", $params)) $conditions[] = parent::add_condition('area_id', $params);

        $q->where($conditions);
        $q->whereRaw("(research.research_state_id = 3 OR research.research_state_id = 4)");
        if (array_key_exists("text", $params)) {
            $q->whereRaw("(LOWER(CONCAT(outcomes.name, research.title)) like LOWER('%$params[text]%') OR LOWER($sql_research_authors) like LOWER('%$params[text]%'))");
        }
        if(array_key_exists("from", $params)) {
            $q->whereRaw("outcomes.date between '$params[from]' and '$params[to]' ");
        }
        if($outcome_types) {
            $q->whereIn('outcomes.type', $outcome_types);
        }

        $query = $q;
		$data = $query->orderBy('date', 'desc')->paginate(10);

		return $data;
	}

    public function public_list_by_year($params) {
        $outcome_types = [];
        if (array_key_exists("type", $params)) {
            // $params['type'] desde el front viene con el valor 4
            $conditions = [['research.status', '=', 1], ['outcomes.status', '=', 1], ['outcomes.type', '=', $params['type']]];
        } else {
            $outcome_types = [1, 4]; // 1: Proyecto, 4: Articulo
            $conditions = [['research.status', '=', 1], ['outcomes.status', '=', 1]];
        }
		$group_by = $params['group_by'];
		$sql_authors = parent::getOutcomeAuthorsSqlPart();

		$sql_research_authors = parent::getResearchAuthorsSqlPart();
		$year_part= parent::getYearSqlPart('outcomes.date');
		// $fields = $group_by?"$year_part as year,count(outcomes.id) as total":"outcomes.id,outcomes.type,outcomes.name,outcomes.date,outcomes.url,outcomes.doi,outcomes.journal, $sql_authors as authors, indexed";
		$fields = "$year_part as year,count(outcomes.id) as total";
		if (array_key_exists("external", $params)) $conditions[] = parent::add_condition('external', $params);
		if (array_key_exists("type_research", $params)) $conditions[] = parent::add_condition('type_research', $params);
        $q = DB::table('outcomes')
            ->select(DB::raw($fields))
            ->join('research', "research.id", '=', 'outcomes.research_id');
        if(array_key_exists("faculty_id", $params)) {
			$q->join('organizations', "organizations.id", '=', 'research.organization_id');
			$conditions[]=['organizations.parent_id','=', $params['faculty_id']];
		}
		$q->where($conditions);
		$q->whereRaw("(research.research_state_id = 3 OR research.research_state_id = 4)");
		if(array_key_exists("text", $params)) {
			$q->whereRaw("(LOWER(CONCAT(outcomes.name, research.title)) like LOWER('%$params[text]%') OR LOWER($sql_research_authors) like LOWER('%$params[text]%'))");
		}
		if (array_key_exists("from", $params)) {
			$q->whereRaw("outcomes.date between '$params[from]' and '$params[to]' ");
		}
        if($outcome_types) {
            $q->whereIn('outcomes.type', $outcome_types);
        }
		$q->groupBy((DB::raw($year_part)));

        $query = $q;
        $data = $query->orderByRaw('year')->get();
		return $data;
	}

	// public function public_list($params){
    //     // dd($params);
	// 	$group_by = array_key_exists("group_by",$params) ? $params['group_by'] : false;
	// 	$sql_authors = parent::getOutcomeAuthorsSqlPart();

	// 	$sql_research_authors = parent::getResearchAuthorsSqlPart();
	// 	$year_part= parent::getYearSqlPart('outcomes.date');
	// 	$fields = $group_by?"$year_part as year,count(outcomes.id) as total":"outcomes.id,outcomes.type,outcomes.name,outcomes.date,outcomes.url,outcomes.doi,outcomes.journal, $sql_authors as authors, indexed";
	// 	$article = 4;
	// 	//Condicion para Articulo
	// 	$conditions = [['research.status', '=', 1], ['outcomes.status', '=', 1], ['outcomes.type', '=', $article]];
	// 	if (array_key_exists("line_id", $params)) $conditions[] = parent::add_condition('line_id', $params);
	// 	if (array_key_exists("program_id", $params)) $conditions[] = parent::add_condition('program_id', $params);

	// 	if (array_key_exists("external", $params)) $conditions[] = parent::add_condition('external', $params);
	// 	if (array_key_exists("type_research", $params)) $conditions[] = parent::add_condition('type_research', $params);
    //     $q = DB::table('outcomes')
	// 	->select(DB::raw($fields))
	// 	->join('research', "research.id", '=', 'outcomes.research_id');

	// 	if( array_key_exists("group_id",$params) ){
	// 		$q->join('categories', "categories.id", '=', 'research.line_id');
	// 		$conditions[]=['categories.parent_id','=', $params['group_id']];
	// 	}
	// 	$q->where($conditions);
	// 	$q->whereRaw("(research.research_state_id = 3 OR research.research_state_id = 4)");
	// 	if (array_key_exists("text", $params)) {
	// 		$q->whereRaw("(LOWER(CONCAT(outcomes.name, research.title)) like LOWER('%$params[text]%') OR LOWER($sql_research_authors) like LOWER('%$params[text]%'))");
	// 	}
	// 	if( array_key_exists("faculty_id",$params) ){
	// 		$q->join('organizations', "organizations.id", '=', 'research.organization_id');
	// 		$conditions[]=['organizations.parent_id','=', $params['faculty_id']];
	// 	}
	// 	if (array_key_exists("from", $params)) {
	// 		$q->whereRaw("outcomes.date between '$params[from]' and '$params[to]' ");
	// 	}
	// 	if($group_by){
	// 		if( $group_by=='year') $group_by= $year_part;
	// 		$q->groupBy((DB::raw($group_by)));
	// 	}
	// 	//Condicion para Proyecto
	// 	$proy_conditions = [
	// 		['research.status', '=', 1],
	// 		['outcomes.status', '=', 1],
	// 		['outcomes.type', '=', 1],
	// 		[DB::raw("(select case count(ro.id) when 0 then null else 1 end from outcomes ro where ro.research_id=research.id and ro.type=$article)")]
	// 	];
	// 	if (array_key_exists("line_id", $params)) $proy_conditions[] = parent::add_condition('line_id', $params);
	// 	if (array_key_exists("program_id", $params)) $proy_conditions[] = parent::add_condition('program_id', $params);

	// 	if (array_key_exists("external", $params)) $proy_conditions[] = parent::add_condition('external', $params);
	// 	if (array_key_exists("type_research", $params)) $proy_conditions[] = parent::add_condition('type_research', $params);

	// 	$q2 = DB::table('outcomes')
	// 		->select(DB::raw($fields))->join('research', "research.id", '=', 'outcomes.research_id');
	// 	if( array_key_exists("group_id",$params) ){
	// 		$q2->join('categories', "categories.id", '=', 'research.line_id');
	// 		$proy_conditions[]=['categories.parent_id','=', $params['group_id']];
	// 	}
	// 	$q2->where($proy_conditions);

	// 	$q2->whereRaw("(research.research_state_id = 3 OR research.research_state_id = 4)");
	// 	if (array_key_exists("text", $params)) {
	// 		$q2->whereRaw("(LOWER(CONCAT(outcomes.name, research.title)) like LOWER('%$params[text]%')
	// 		OR $sql_research_authors like LOWER('%$params[text]%'))");
	// 	}
	// 	if( array_key_exists("faculty_id",$params) ){
	// 		$q2->join('organizations', "organizations.id", '=', 'research.organization_id');
	// 		$conditions[]=['organizations.parent_id','=', $params['faculty_id']];
	// 	}
	// 	if (array_key_exists("from", $params)) {
	// 		$q2->whereRaw("outcomes.date between '$params[from]' and '$params[to]' ");
	// 	}
	// 	if (array_key_exists("type", $params)) {
	// 		if ($params['type'] == $article) {
	// 			$query = $q;
	// 		} else {
	// 			$query = $q2;
	// 		}
	// 	} else {
	// 		// $query = $q2->union($q); // original
    //         $query = $q2;
	// 	}
	// 	// dd($query->orderBy('date', 'desc')->toSql());
	// 	//$q2->union($q);
	// 	// echo( $query->orderBy('date', 'desc')->toSql());exit;
	// 	//return $q2->get();

	// 	if($group_by){
	// 		if( $group_by=='year') $group_by=  parent::getYearSqlPart("outcomes.date");
	// 	    $query->groupBy((DB::raw($group_by)));
	// 		if( array_key_exists("sql", $params) ) exit($query->toSql());
	// 		$data = $query->orderByRaw(' year ')->get();
	// 	}else{
	// 		$data =$query->orderBy('date', 'desc')->paginate(10);
	// 	}

	// 	return $data;
	// }

	public function py_by($params){
		$sql_authors = parent::getResearchAuthorsSqlPart();
		$fields = "research.*, research_states.name as research_state, $sql_authors  as authors, area.name as area, group_category.name as group, line.name as line";
		$conditions = [['research.status', '=', 1]];
		$conditions = [['research.external', '=', 0]];
        // dd($params);
		if (array_key_exists("grade", $params)) { $conditions[] = ['grade', '=', $params['grade']];}
		if (array_key_exists("type_research", $params)) { $conditions[] = ['type_research', '=', $params['type_research']]; }
		if (array_key_exists("organization_id", $params)) { $conditions[] = ['research.organization_id', '=', $params['organization_id']]; }
		if (array_key_exists("research_state_id", $params)) {$conditions[] = ['research_state_id', '=', $params['research_state_id']];}
        // dd($fields);
		$q = DB::table('research')
			->select(DB::raw($fields))
			->join('research_states', 'research_states.id', '=', 'research.research_state_id')
            ->join('categories as area', 'area.id', '=', 'research.area_id')
            ->join('categories as group_category', 'group_category.id', '=', 'research.group_id')
            ->join('categories as line', 'line.id', '=', 'research.line_id')
			->where($conditions);
			if (array_key_exists("from", $params)) {
                $q->whereRaw("date_init  BETWEEN '$params[from]' AND '$params[to]'");
            }

		return $q->orderBy('research.id', 'desc')->get();
		//echo( $q->toSql());exit;
	}

	public function py_by_author($params)
	{
		$sql_authors = parent::getResearchAuthorsSqlPart();
		$year_date_init = parent::getYearSqlPart('research.date_init');
		$year_date_end = parent::getYearSqlPart('research.date_end');
		$fields = "type_research, research_authors.role, people.name, people.lastname, people.type, people.id as author_id, $year_date_init as year_init, 	$year_date_end as year_end, research.id as research_id,research.title, research.code, research_states.name as research_state,research.document as document,
		$sql_authors as authors";
		//$conditions = [['research.status', '=', 1],['external', '=',0]];
		$conditions = [['research.status', '=', 1],['external', '=',0]];

		if (array_key_exists("grade", $params)) { $conditions[] = ['grade', '=', $params['grade']];}
		if (array_key_exists("type_research", $params)) { $conditions[] = ['type_research', '=', $params['type_research']]; }
		if (array_key_exists("author_id", $params)) {$conditions[] = ['people.id', '=', $params['author_id']];}

		$q = DB::table('people')
			->select(DB::raw($fields))
			->join('research_authors', 'research_authors.author_id', '=', 'people.id')
			->join('research', 'research.id', '=', 'research_authors.research_id')
			->join('research_states', 'research_states.id', '=', 'research.research_state_id')
			->where($conditions);
			//$q->whereRaw("(research_state_id = 3 OR research_state_id = 4)") ;//ejecucion, culmindo.
			if (array_key_exists("from", $params)) {$q->whereRaw("date_init  BETWEEN '$params[from]' AND '$params[to]' ") ;}
			//echo( $q->toSql());exit;
		return $q->orderBy('date_end', 'desc')->get();
	}

	public function constancy_by_author($params)
	{
		$sql_authors = parent::getResearchAuthorsSqlPart();
		$year_date_end= parent::getYearSqlPart('research.date_end');
		$fields = "people.name, people.lastname,people.id as author_id ,$year_date_end as year,research.id as research_id,research.title, research.code, research_states.name as research_state,research.document as document,research.date_init,research.date_end, $sql_authors  as authors";
		$conditions = [['research.status', '=', 1],['research_states.id', '=', 3]]; //Si está en Ejecución

		if (array_key_exists("author_id", $params)) {
			$conditions[] = ['people.id', '=', $params['author_id']];
		}
		if (array_key_exists("type_research", $params)) {
			$conditions[] = ['type_research', '=', $params['type_research']];
		}
		if (array_key_exists("grade", $params)) {
			$conditions[] = ['grade', '=', $params['grade']];
		}
		$q = DB::table('people')
			->select(DB::raw($fields))
			->join('research_authors', 'research_authors.author_id', '=', 'people.id')
			->join('research', 'research.id', '=', 'research_authors.research_id')
			->join('research_states', 'research_states.id', '=', 'research.research_state_id')
			->where($conditions);
			if (array_key_exists("from", $params)) {$q->whereRaw("date_init  BETWEEN '$params[from]' AND '$params[to]' ") ;}
		return $q->get();
		// echo( $q->toSql());exit;
	}

	public function certified_by_author($params)
	{
		$sql_authors = parent::getResearchAuthorsSqlPart(true);
		$year_date_init = parent::getYearSqlPart("research.date_init");
		$fields = "people.name, people.lastname,people.id as author_id ,$year_date_init as year,research.id as research_id,research.title, research.code, research_states.name as research_state,research.document as document, research.date_init,research.date_end, $sql_authors  as authors";
		$conditions = [['research.status', '=', 1],['research.research_state_id', '=', 4]]; //Si está culminado
		if (array_key_exists("author_id", $params)) {
			$conditions[] = ['people.id', '=', $params['author_id']];
		}
		if (array_key_exists("type_research", $params)) {
			$conditions[] = ['type_research', '=', $params['type_research']];
		}
		if (array_key_exists("grade", $params)) {
			$conditions[] = ['grade', '=', $params['grade']];
		}
		$q = DB::table('people')
			->select(DB::raw($fields))
			->join('research_authors', 'research_authors.author_id', '=', 'people.id')
			->join('research', 'research.id', '=', 'research_authors.research_id')
			->join('research_states', 'research_states.id', '=', 'research.research_state_id')
			->where($conditions);
			if (array_key_exists("from", $params)) {$q->whereRaw("date_init  BETWEEN '$params[from]' AND '$params[to]' ") ;}
		return $q->orderBy("research.date_init","desc")->get();
	}

	public function sunedu_list($params)
	{
		$sql_authors = parent::getResearchAuthorsSqlPart();
        $group_concat_outcome = parent::getGroupConcatSqlPart("o.id,'|',o.type");
        $fields = "organizations.name as college,research.id,research.title,research.objectives, area.name as area, group_category.name as group, line.name as line, $sql_authors as authors, date_init,date_end, budget,fin_company,fin_type,(select $group_concat_outcome from outcomes o where o.research_id=research.id and type>1 and o.status=1 group by o.research_id ) as results";
        $conditions = [['research.status', '=', 1],['research.type_research', '=', 2],];
		if (array_key_exists("research_state_id", $params)) {
			$conditions[] = ['research_state_id','=',$params['research_state_id']];
		}
		$q = DB::table('research')
			->select(DB::raw($fields))
			->join('organizations', "organizations.id", '=', 'research.organization_id')
            ->join('categories as area', 'area.id', '=', 'research.area_id')
            ->join('categories as group_category', 'group_category.id', '=', 'research.group_id')
            ->join('categories as line', 'line.id', '=', 'research.line_id')
			->join('research_authors', "research_authors.research_id", '=', 'research.id')
			->join('people', "research_authors.author_id", '=', 'people.id')
			->where($conditions);
        if (array_key_exists("type", $params)) {
        $q->whereRaw("people.type='$params[type]'");
        }
		if (array_key_exists("from", $params)) {$q->whereRaw("date_init  BETWEEN '$params[from]' AND '$params[to]' ") ;}
		return $q->groupBy( DB::raw('research.id, organizations.id, area.id, group_category.id, line.id') )->get();
		//echo $q->toSql();exit;
	}

	private function createCode($params){
		$type=$params['type_research'];
		$college= DB::table('organizations')->where('id',$params["organization_id"])->first();
		$last_research=$this->model->where(['type_research'=>$type,'organization_id'=>$college->id,'status'=>1])->get();
		$correlative = count($last_research)+1;
		$correlative = str_pad($correlative, 4, "0", STR_PAD_LEFT);
		$type= $type=='1' ? 'T' : ($type=='2' ? 'D' : ($type=='3' ? 'E' : ($type=='4' ? 'I' : '-')));
		$year=date('Y');
		$month = date("n");
		$period = ceil($month / 3);
		$code_format="$type.$college->code/$year.$period/$correlative";
		return $code_format;
	}

	public function save($params)
	{
		if (array_key_exists("research_authors", $params)) {
			$authors = $params['research_authors'];
			unset($params['research_authors']);
		}
		if (array_key_exists('id', $params)) {
			$o = $this->model->find($params['id']);
			$o->update($params);
		} else {
			$code=$this->createCode($params);
			$params['code']=$code;
			$o = $this->model->create($params);
			\App\Models\ResearchLog::create(["research_id" => $o->id, 'new_status_id' => 2, 'user_id' => $params['user_id'], 'date_at' => new \Datetime()]);
		}
		\App\Models\Person::where(["id"=>$params['user_id']])->update([ 'group_id'=>$params['group_id'], 'line_id'=>$params['line_id'] ]);


		if ($authors) {
			\App\Models\ResearchAuthor::where(['research_id' => $o->id])->update(['status' => 0]);
			foreach ($authors as $author_data) {
				$author = \App\Models\ResearchAuthor::where(['research_id' => $o->id, 'author_id' => $author_data['id']])->first();
				if ($author) $author->update(['status' => 1, 'role' => $author_data['role']]);
				else \App\Models\ResearchAuthor::create(['research_id' => $o->id, 'author_id' => $author_data['id'], 'role' => $author_data['role']]);
			}
		}
		return $o;
	}

	public function get_author($people_id){
		$person= \App\Models\Person::find($people_id);
		$faculty=(object)['name'=>'--','code'=>'--'];
		$college = \App\Models\Organization::find($person->organization_id);
		if($college) $faculty= \App\Models\Organization::find($college->parent_id);
		if($person->condition=='N')$condition="nombrado";
		else $condition="contratado";
		return ['id'=>$person->id,'name'=>$person->name,'lastname'=>$person->lastname,'type'=>$person->type,'condition'=>$condition,'degree'=>$person->degree,'faculty'=>$faculty,'college'=>$college];
	}

	public function listStatus($params){
		$date_at_f = parent::getDateFormatSql('research_logs.date_at', 'd/m/Y');
			return DB::table("research_logs")
			->select( DB::raw("research_logs.*, people.name as user_name, $date_at_f as date_at"))
			->join("users","users.id","=","research_logs.user_id")
			->join("people","users.id","=","people.user_id")
			->where( ["research_id"=>$params['research_id']] )
			->orderBy('id', 'ASC')->get();
	}

	public function saveStatus($params)
	{
		$r = $this->model->find($params['research_id']);
		$r->update(['research_state_id' => $params['new_status_id']]);
		if (array_key_exists('id', $params)) {
			$o = \App\Models\ResearchLog::find($params['id']);
			$o->update($params);
		} else {
			$o = \App\Models\ResearchLog::create(['date_at' => date('Y-m-d H:i:s'), 'research_id' => $params['research_id'], 'new_status_id' => $params['new_status_id'], 'user_id' => $params['user_id']]);
		}
		return $o;
	}

	public function delete($id)
	{
		\App\Models\Research::find($id)->update(['status' => 0]);
	}

	public function by_period($params){
		$sql_authors = parent::getResearchAuthorsSqlPart();
        $conditions = [['research.status','=',1]];
		$conditions = [['research.external','=',0]];
		$conditions[] = parent::add_condition('type_research', $params);
		$conditions[] = parent::add_condition('research_state_id', $params);
		if(array_key_exists('grade',$params) ) $conditions[] = parent::add_condition('grade', $params);
		$conditions[] = $this->add_condition('organization_id', $params,'research');
        $date_init_f = parent::getDateFormatSql('research.date_init','d/m/Y');
		$date_end_f = parent::getDateFormatSql('research.date_end','d/m/Y');
		$date_at_f = parent::getDateFormatSql('research_logs.date_at','d/m/Y');
        $fields = "research.id, research.title, research.code, $date_init_f as date_init, $date_end_f as date_end, $date_at_f as log_date_at, research_state_id, organizations.name as organization, $sql_authors as authors, area.name as area, group_category.name as group, line.name as line";
        // dd($fields);

        $q = DB::table('research')
            -> select(DB::raw($fields))
            ->join('research_logs','research.id', '=','research_logs.research_id')
            ->join('organizations','research.organization_id', '=','organizations.id')
            ->join('categories as area', 'area.id', '=', 'research.area_id')
            ->join('categories as group_category', 'group_category.id', '=', 'research.group_id')
            ->join('categories as line', 'line.id', '=', 'research.line_id')
            ->where($conditions);
		$year_date_at = parent::getYearSqlPart("research_logs.date_at");
        // dd($params);
        if( array_key_exists("year",$params) ){ $q->whereRaw("$year_date_at='$params[year]'");}
		// if( array_key_exists("period",$params) ){ $q->whereRaw(  parent::getDatePart($params['period_type'],'research_logs.date_at' )."=$params[period]");}

		$q->groupBy(DB::raw("research.id, $date_at_f, organizations.id, research_logs.id, area.id, group_category.id, line.id"))->orderBy('research.id','asc')
		->orderBy('research_logs.id','desc');
		// exit($q->toSql());
		return $q->get();
    }
	public function byPeriodWord($phpWord, $section, $list, $params){
		$cellHCentered = array('align' => 'center');
		$cellVCentered = array('valign' => 'center');
		$cellRowContinue = array('vMerge' => 'continue');
		$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
		$cellColSpan = array('gridSpan' => 3, 'valign' => 'center', 'bgColor' =>'FAFAFA');

		$section->addText("$params[type_research] $params[research_state_id] ".($params['period_type']=='T'?'AL TRIMESTRE':'A')." $params[period] DEL $params[year]" , ['bold'=>true]);
		$section->addText(" ");
		foreach ($list as  $o) {
			$section->addText($o['org'], ['bold'=>true]);
			$section->addText("");
			foreach($o['research'] as $r){
                $section->addText($r["code"],['bold'=>true, 'size'=>8]);
                $section->addText($r["title"]);
                $section->addText("Responsables:",['bold'=>true, 'size'=>8]);
                foreach($r['authors'] as $a){
                    $section->addText(($a['role']==''?'':"$a[role]: ")."$a[name]",['size'=>9]);
                    //$section->addTextBreak();
                }
                $section->addText("");
			}
			/*
			$table = $section->addTable();
			$table->addRow(400);
			$table->addCell(9000,	$cellColSpan)->addText( $o['org'], ['bold'=>true]);
			$table->addRow(400);
			$table->addCell(5000,$cellVCentered)->addText( 'INVESTIGACIÓN', ['bold'=>true, 'size'=>8] );
			$table->addCell(3000,$cellVCentered)->addText( 'AUTORES', ['bold'=>true, 'size'=>8]);
			foreach($o['research'] as $r){
				$table->addRow();
				$run=$table->addCell(5000)->addTextRun();
				$run->addText($r["code"],['bold'=>true, 'size'=>8] );$run->addTextBreak();
				$run->addText($r["title"]);
				$textrun=$table->addCell(3000)->addTextRun();
				foreach($r['authors'] as $a){
					$textrun->addText("$a[name]" .($a['role']==''?'':" ($a[role])"  ), ['size'=>9] );$textrun->addTextBreak();
				}
			}
			*/
		}
	}

	public function byAuthorWord($phpWord, $section, $list, $params){
		$cellHCentered = array('align' => 'center');
		$cellVCentered = array('valign' => 'center');
		$cellRowContinue = array('vMerge' => 'continue');
		$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
		$cellColSpan = array('gridSpan' => 3, 'valign' => 'center', 'bgColor' =>'FAFAFA');

		$section->addText("RELACIÓN DE $params[type_research] DE ".strtoupper($params['author']['name']." ". $params['author']['lastname']) , ['bold'=>true]);
		$section->addText(" ");
		$section->addText(" ");

		$table = $section->addTable();
			$table->addRow(500);
			$table->addCell(300,$cellVCentered)->addText( 'N', ['bold'=>true, 'size'=>9] );
			$table->addCell(5100,$cellVCentered)->addText( 'Título', ['bold'=>true, 'size'=>9]);
			$table->addCell(3000,$cellVCentered)->addText( 'Autores', ['bold'=>true, 'size'=>9]);
			$table->addCell(1300,$cellVCentered)->addText( 'Documento', ['bold'=>true, 'size'=>9]);
			$table->addCell(1000,$cellVCentered)->addText( 'Estado', ['bold'=>true, 'size'=>9]);
			$table->addCell(400,$cellVCentered)->addText( 'Año', ['bold'=>true, 'size'=>9]);
			foreach($list as $i => $r){
				$table->addRow();
				$table->addCell()->addText($i+1 );
				$table->addCell()->addText($r["title"] );
				$textrun=$table->addCell()->addTextRun();
				foreach($r['authors'] as $a){
					$textrun->addText("$a[name]" .($a['role']=='Titular'?" (T)":""  ), ['size'=>9] );$textrun->addTextBreak();
				}
				$table->addCell()->addText( $r['document'],  ['size'=>9] );
				$table->addCell()->addText( $r['research_state'],  ['size'=>8] );
				$table->addCell()->addText( $r['year_init'],  ['size'=>9] );
			}
	}

	private function certifiedTablePart($section,$list,$asesored,$id ){
		$listStyle = array('listType'=>\PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER);
		if($asesored){
			foreach($list as $i => $r){
				//$section->addListItem($r["title"], 0,null, $listStyle);
				$listItemRun = $section->addListItemRun("",$listStyle,['lineHeight'=>1.5,'alignment'=>'both']);
				$listItemRun->addText($r["title"].". ");
				foreach($r['authors'] as $a){
					if($a['role']=='Titular')
				//$t.="Tesista: ".$a["name"].". ";
				$listItemRun->addText("Tesista: ".$a["lastname"]." ".$a["name"],['bold'=>true]);
				}
				$listItemRun->addText(". Año: ".$r["year"],['bold'=>true]);
				//$section->addText(['bold'=>true], $listStyle);
			}
		}
		else{
			foreach($list as $i => $r){
				$listItemRun = $section->addListItemRun("",$listStyle,['lineHeight'=>1.5,'alignment'=>'both']);
				foreach($r['authors'] as $k=> $a){
					if($a['id']!=$id)
					$listItemRun->addText($a["lastname"]." ".$a["name"] .($k+1<count($r['authors'])?'; ':'' ));
					else
					$listItemRun->addText($a["lastname"]." ".$a["name"] .($k+1<count($r['authors'])?'; ':'' ),['bold'=>true]);
				}
				$listItemRun->addText(". ".$r['year']. ". ",['bold'=>true]);
				$listItemRun->addText($r['title']);
			}
		}
	}

	private function constancyTablePart($section,$list ){
		$listStyle = array('listType'=>\PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER);
			foreach($list as $i => $r){
				$section->addListItem($r['title'], 0, ['bold'=>true,], $listStyle,['alignment'=>'both']);
		}
	}

	public function constancyWord($phpWord, $section, $list, $params){
		//dd($params);
		$section->addText("CONSTANCIA Nº [NNN]– ".$params['author']['faculty']->code.".UNAS-T.M.",['bold'=>true, 'underline'=>'single'],['alignment'=>'center']);
		$section->addText(" ");
		$section->addText("EL JEFE DE LA UNIDAD DE GESTIÓN DE LA INVESTIGACIÓN DE LA UNIVERSIDAD NACIONAL AGRARIA DE LA SELVA, QUE SUSCRIBE," , ['bold'=>true],['lineHeight'=>1.5, 'alignment'=>'both']);
		$section->addText(" ");
		$section->addText("HACE CONSTAR:" , ['bold'=>true],['lineHeight'=>1.5]);
		$section->addText(" ");
		$textrun=$section->addTextRun(['lineHeight'=>1.5,'alignment'=>'both']);
		$textrun->addText("Que, el ".$params['author']['degree'] ." ",[],);
		$textrun->addText($params['author']['name'] ." ".$params['author']['lastname'], ['bold'=>true]);
		$textrun->addText(", ".( $params['author']['type']=='D'?'Docente':'')." ".($params['author']['condition']=='N'?'nombrado':'').", adscrito a la ".$params['author']['faculty']->name." de esta Casa Superior de Estudios, en calidad de ");
		$textrun->addText("EJECUTOR",['bold'=>true]);
		$textrun->addText(", se encuentra realizando los trabajos de investigación siguientes: ");
		$section->addText(" ");
		$this->constancyTablePart($section,$list);
		$section->addText(" ");
		$section->addText("Se expide la presente constancia a solicitud del interesado para los fines y usos a la que hubiere lugar. Se expide la presente constancia a solicitud del interesado para los fines y usos a la que hubiere lugar.Se expide la presente constancia a solicitud del interesado para los fines y usos a la que hubiere lugar.", null, ['lineHeight'=>1.5,'alignment'=>'both']);
		$section->addText(" ");
		$section->addText("Tingo María, $params[now]",null,['alignment'=>'right']);
		$section->addText(" ");$section->addText(" ");$section->addText(" ");	$section->addText(" ");
		$section->addText("Firma.");	$section->addText(" ");
		$section->addText("C.c. Archivo.");
	}

	public function certifiedWord($phpWord, $section, $list, $params){
		$section->addText("CERTIFICADO Nº NNN– ".$params['author']['faculty']->code.".UNAS-T.M.", ['bold'=>true,'underline'=>'single'],['lineHeight'=>1.5,'alignment'=>'center']);
		$section->addText(" ");
		$section->addText("EL JEFE DE LA UNIDAD DE GESTIÓN DE LA INVESTIGACIÓN DE LA UNIVERSIDAD NACIONAL AGRARIA DE LA SELVA QUE SUSCRIBE," , ['bold'=>true],['lineHeight'=>1.5,'alignment'=>'both']);
		$section->addText(" ");
		$section->addText("CERTIFICA:" , ['bold'=>true],['lineHeight'=>1.5]);
		$section->addText(" ");
		$textrun=$section->addTextRun(['lineHeight'=>1.5,'alignment'=>'both']);
		$textrun->addText("Que, el ".$params['author']['degree'] ." ",[],['lineHeight'=>1.5]);
		$textrun->addText($params['author']['name'] ." ".$params['author']['lastname'], ['bold'=>true]);
		$textrun->addText(", ".( $params['author']['type']=='D'?'Docente':'')." ".($params['author']['condition']=='N'?'nombrado':'').", adscrito a la ".$params['author']['faculty']->name." de esta Casa Superior de Estudios");
		if($params['role']=="Asesor")
		$textrun->addText("ha participado en calidad de ASESOR DE TESIS".$params['grade']." y es parte de la autoría de Artículos Científicos de los siguientes egresados:");
		else
		$textrun->addText(" en condición de ".$params['role']." ha desarrollado y cumplido en presentar los Informes Trimestrales e Informes Finales de los trabajos de investigación siguientes");
		$section->addText(" ");
		if($params['role']=="Asesor") $this->certifiedTablePart($section,$list,1,$params['author_id']);
		else $this->certifiedTablePart($section,$list,0,$params['author_id']);
			$section->addText("Los mencionados trabajos de investigación se encuentran en los archivos de esta Oficina y supervisado por la Unidad de Gestión de Investigación.",null,['lineHeight'=>1.5,'alignment'=>'both']);
			$section->addText("Se expide el presente certificado a solicitud del interesado para los fines y usos a la que hubiere lugar. ",null,['lineHeight'=>1.5,'alignment'=>'both']);
			$section->addText(" ");
			$section->addText("Tingo María, $params[now]",null,['alignment'=>'right']);
			$section->addText(" ");$section->addText(" ");$section->addText(" ");	$section->addText(" ");	$section->addText(" ");
			$section->addText("Firma.");	$section->addText(" ");
			$section->addText("C.c. Archivo.");
	}

	public function byStateWord($phpWord, $section, $data, $params){
		$cellHeader = array('valign' => 'center', 'bgColor' =>'DDDDDD');
		$state = $this->r_states[$params['research_state_id']];
		$type = $this->r_types[$params['type_research']];
		//$grade = $this->$grades[$params['grade']];
		$section->addText("Investigaciones Tipo $type en Estado $state",['bold'=>true,'size'=>14],['alignment'=>'center']);
		$section->addText("Del $params[from] al $params[to]",['bold'=>true,'size'=>14],['alignment'=>'center']);
		$section->addText("");
		//$section->addText($r['research_state']);
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(1000,$cellHeader )->addText( 'N°');
		$table->addCell(1000, $cellHeader )->addText( ' TITULO');
		$table->addCell(6000, $cellHeader )->addText( 'AUTORES');
		$table->addCell(4000,$cellHeader  )->addText( 'DOCUMENTO');
		$i = 1;
		//dd($data);
		foreach($data as $r){
			$table->addRow();
			$table->addCell(1000)->addText($i++);
			//$table->addCell(5000)->addText('--');
			$table->addCell(5000)->addText($r['title'].".");

			$textrun=$table->addCell()->addTextRun();
			foreach($r['authors'] as $a){
				$textrun->addText("$a[name]" .($a['role']=='Titular'?" (T)":""  ), ['size'=>9] );
				$textrun->addTextBreak();
			}
			$table->addCell(3000)->addText($r['document']);
		}
	}

	public function byCollegeWord($phpWord, $section, $data, $params){
		$cellHeader = array('valign' => 'center', 'bgColor' =>'DDDDDD');
		//$section->addText($r['research_state']);
		//$section->addText($params['college']);
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(1000, $cellHeader)->addText( 'N°');
		$table->addCell(1000, $cellHeader)->addText( ' TITULO');
		$table->addCell(6000,$cellHeader)->addText( 'AUTORES');
		$table->addCell(4000,$cellHeader)->addText( 'DOCUMENTO');
		$i = 1;
		//dd($data);
		foreach($data as $r){
			$table->addRow();
			$table->addCell(1000)->addText($i++);
			//$table->addCell(5000)->addText('--');
			$table->addCell(5000)->addText($r['title'].".");

			$textrun=$table->addCell()->addTextRun();
			foreach($r['authors'] as $a){
				$textrun->addText("$a[name]" .($a['role']=='Titular'?" (T)":""  ), ['size'=>9] );$textrun->addTextBreak();
			}
			//if($i==21) break;
			$table->addCell(3000)->addText($r['document']);
		}

	}
}
