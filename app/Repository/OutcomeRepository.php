<?php

namespace App\Repository;

use App\Models\Outcome;
use App\Repository\OutcomeRepositoryI;
use Illuminate\Support\Facades\DB; 

class OutcomeRepository extends BaseRepository implements OutcomeRepositoryI{
	public function __construct(Outcome $model){
		parent::__construct($model);
	}
	
	public function all($params){
		$sql_authors = parent::getOutcomeAuthorsSqlPart(array_key_exists('author_full', $params)?'full':false);
		$is_sql=array_key_exists('sql',$params)?true:false;
		$inc_lines = array_key_exists('include_lines', $params)?true:false;
		$inc_groups = array_key_exists('include_groups', $params)?true:false;
		$xpage = array_key_exists('xpage', $params)?$params['xpage']:10;
		$conditions=['outcomes.status'=>1, 'research.status'=>1];
		if (array_key_exists('research_id', $params)) { $conditions['outcomes.research_id'] = $params['research_id'];}
		if (array_key_exists('approved', $params)) {$conditions['outcomes.approved'] = $params['approved'];	}
		if (array_key_exists('period_type', $params)) {$conditions['outcomes.period_type'] = $params['period_type'];	}
		if (array_key_exists('period', $params)) {$conditions['outcomes.period'] = $params['period'];	}
		if (array_key_exists('public', $params)) {$conditions['outcomes.public'] = $params['public'];}
		if (array_key_exists('incentive', $params)) {$conditions['research.incentive'] = $params['incentive'];}	
		if (array_key_exists('external', $params)) {$conditions['research.external'] = $params['external'];}
		$sq_authors =	$sql_authors ;
		$date_init_f = parent::getDateFormatSql('research.date_init', 'd/m/Y');
		$date_end_f = parent::getDateFormatSql('research.date_end', 'd/m/Y');
		$approved_date_f = parent::getDateFormatSql('outcomes.approved_date', 'd/m/Y');
		$reviewed_date_f = parent::getDateFormatSql('outcomes.reviewed_date', 'd/m/Y');
		$outcome_date_f = parent::getDateFormatSql('outcomes.date', 'd/m/Y');
	
		$q=DB::table('outcomes')
		-> select('outcomes.*', 'reviewed_p.name as reviewed_by_user', 'approved_p.name as approved_by_user','research.document', 'research.type_research','research.title',
		DB::raw("$reviewed_date_f as reviewed_date,$approved_date_f as approved_date,$date_end_f  as research_date_end, $date_init_f  as research_date_init,$outcome_date_f  as date, $sq_authors  as authors, (select count(outcomes2.id) from outcomes outcomes2 where outcomes2.research_id = research.id and outcomes2.type = outcomes.type and outcomes2.id < outcomes.id AND outcomes2.status = 1 ) + 1  as count ". ($inc_lines?",research.line_id , categories.name as line":"") . (
			$inc_groups?
				(",research.group_id , categories.name as " .( parent::getPDODriver()=='pgsql'?"group":"'group'" ) ):"")  ));
		$q->join('research', 'research.id', '=', 'outcomes.research_id');
		if (array_key_exists('faculty_id', $params)) {
			$q->join('organizations', "organizations.id", '=', 'research.organization_id');
			$conditions[]=['organizations.parent_id','=', $params['faculty_id']];
		}
		if (array_key_exists('group_id', $params)) {
			$q->join('categories', "categories.id", '=', 'research.line_id');
			$conditions[]=['categories.parent_id','=', $params['group_id']];
		}
		if ($inc_lines) {
			$q->join('categories', "categories.id", '=', 'research.line_id');
		}
		if ($inc_groups) {
			$q->join('categories', "categories.id", '=', 'research.group_id');
		}
		
		$q->leftjoin('people as approved_p', 'approved_p.id', '=', 'outcomes.approved_by')
		->leftjoin('people as reviewed_p', 'reviewed_p.id', '=', 'outcomes.reviewed_by')
		->where($conditions);
		$sql_year_outcome = parent::getYearSqlPart('outcomes.date');
		if( array_key_exists("year",$params) ){ 	$q->whereRaw("	$sql_year_outcome  = '$params[year]' ");}
		if($is_sql) 
		exit($q->toSql()); 
		return $q->latest()->paginate(	$xpage);
	}

	public function scientific_initiation($params){
    $conditions=[['research.status','=',1],['outcomes.status','=',1],['outcomes.approved','=',1], ['outcome_authors.role',"=", 'IC' ], [ 'research.incentive','=',1 ], [ 'research.external','=',0 ]];
    
		if( array_key_exists("type_research",$params) ){ 
			$conditions[]=['type_research','=', $params['type_research']];
			if($params['type_research']==1) $conditions[]=['research.grade','=', 1]; // cuando es tesis, solo de pregrado.
		 }
    if( array_key_exists("period_type",$params) ){$conditions[]=['period_type','=', $params['period_type']];  }
    if( array_key_exists("period",$params) ){$conditions[]=['period','=', $params['period']];  }
		$outcome_date_f = parent::getDateFormatSql('outcomes.date');
		$date_init_f = parent::getDateFormatSql('research.date_init','d/m/Y');
		$date_end_f = parent::getDateFormatSql('research.date_end','d/m/Y');
    $fields="people.id as id, dni, concat(people.name,' ',people.lastname) as author,$outcome_date_f as date, $date_init_f  as  date_init,  $date_end_f as date_end ,fac.name as organization";
    $q =DB::table('research')
    -> select(DB::raw($fields))
    ->join('outcomes','research.id', '=','outcomes.research_id')
    ->join('outcome_authors','outcomes.id', '=','outcome_authors.outcome_id')
    ->join('people','people.id', '=','outcome_authors.author_id')
		->join('organizations as col','research.organization_id', '=','col.id')
		->join('organizations as fac','col.parent_id', '=','fac.id')
    ->where($conditions);
		$year_outcome_date= parent::getYearSqlPart("outcomes.date");
    if( array_key_exists("year",$params) ){  $q->whereRaw("$year_outcome_date='$params[year]'");}
		return $q->groupBy( DB::raw('people.id, outcomes.date, research.date_init, research.date_end, fac.id') )->orderBy('fac.id','asc')->get();
  }

	public function investigations_by_trimester($params){
    $conditions=[['research.status','=',1],['outcomes.status','=',1],['incentive','=',1],['outcomes.approved','=',1]];
		if( array_key_exists('person_type',$params) ) 	$conditions[]=['people.type','=', $params['person_type']];
		if( array_key_exists('condition',$params) ) 	$conditions[]=['people.condition','=', $params['condition']];
		if( array_key_exists("type_research",$params) ){ 
			$conditions[]=['type_research','=', $params['type_research']];
			if($params['type_research']==1) $conditions[]=['research.grade','=', 1]; // cuando es tesis, solo de pregrado.
		 }
    if( array_key_exists("period_type",$params) ){$conditions[]=['period_type','=', $params['period_type']];  }
    if( array_key_exists("period",$params) ){$conditions[]=['period','=', $params['period']];  }
		$outcome_date_f = parent::getDateFormatSql('outcomes.date','d/m/Y');
		$date_init_f = parent::getDateFormatSql('research.date_init','d/m/Y');
		$date_end_f = parent::getDateFormatSql('research.date_end','d/m/Y');
    //$fields="people.id as id, concat(people.name,' ',people.lastname) as author, dni,$outcome_date_f  as date, $date_init_f as  date_init,  $date_end_f as date_end, fac.name as organization";
    $fields="people.id as id, concat(people.name,' ',people.lastname) as author, dni, fac.name as organization";
    $q =DB::table('research')
    -> select(DB::raw($fields))
    ->join('outcomes','research.id', '=','outcomes.research_id')
    ->join('outcome_authors','outcomes.id', '=','outcome_authors.outcome_id')
    ->join('people','people.id', '=','outcome_authors.author_id')
		->join('organizations as col','research.organization_id', '=','col.id')
		->join('organizations as fac','col.parent_id', '=','fac.id')
    ->where($conditions);
		$year_outcome_date= parent::getYearSqlPart("outcomes.date");
    if( array_key_exists("year",$params) ){  $q->whereRaw("$year_outcome_date='$params[year]'");}
		$q->groupBy(DB::raw('people.id, fac.id'))->orderBy('fac.id','asc');
		//exit($q->toSql());
		return $q->get();
  }

	public function out_by($params){

    $conditions=[['research.status','=',1],['outcomes.status','=',1],['outcomes.approved','=','1'],['research.external','=','0']];
    if( array_key_exists("type_research",$params) ){ $conditions[]=['type_research','=', $params['type_research']]; }
    if( array_key_exists("type",$params) ){ $conditions[]=['outcomes.type','=', $params['type']]; }
		if( array_key_exists("period_type",$params) ){$conditions[]=['period_type','=', $params['period_type']];  }
    if( array_key_exists("period",$params) ){$conditions[]=['period','=', $params['period']];  }
    if( array_key_exists("organization_id",$params) ){$conditions[]=['organizations.id','=', $params['organization_id']];  }
    if( array_key_exists("author_id",$params) ){$conditions[]=['author_id','=', $params['author_id']];  }
    if( array_key_exists("indexed",$params) ){$conditions[]=['indexed','=', $params['indexed']];  }
    if( array_key_exists("in_journal",$params) ){$conditions[]=['journal','!=', ''];  }	
    if( array_key_exists("by_author",$params) ){$conditions[]=['outcome_authors.status','=',1];  }	

		$sql_authors = parent::getOutcomeAuthorsSqlPart();
		$outcome_date_f = parent::getDateFormatSql('outcomes.date');
    $fields="outcomes.*, 	$outcome_date_f  as date, $sql_authors  as authors";
    $q =DB::table('outcomes')
    -> select(DB::raw($fields))
		->join("research","research.id","=","outcomes.research_id")
		->join('organizations','research.organization_id', '=','organizations.id');
    if( array_key_exists("by_author",$params) ){  $q->join('outcome_authors','outcomes.id', '=','outcome_authors.outcome_id');  }	
    $q->where($conditions);
		if (array_key_exists("from", $params)) $q->whereRaw("outcomes.date between '$params[from]' and '$params[to]' ");
		$year_outcome_date= parent::getYearSqlPart("outcomes.date");
    if( array_key_exists("year",$params) ){  $q->whereRaw("$year_outcome_date='$params[year]'");}
		if( array_key_exists("from",$params) ){  $q->whereRaw("outcomes.date between '$params[from]' and '$params[to]'");}
		 return $q->orderBy('organizations.id','asc')->get();
  }

	public function save_pending_outcomes($params){
		$user_id = $params['outcomes'][0]['approved_by'];
		$ids = array_map(function($item){
			return $item['id'];
		}, $params['outcomes']);
		\App\Models\Outcome::wherein('id', $ids)->update(['approved'=>1, 'approved_date'=> new \DateTime(), 'approved_by'=>$user_id]);
	}

	public function save_reviewed_pending_outcomes($params){
		$user_id = $params['outcomes'][0]['reviewed_by'];
		$ids = array_map(function($item){
			return $item['id'];
		}, $params['outcomes']);
		\App\Models\Outcome::wherein('id', $ids)->update(['reviewed'=>1, 'reviewed_date'=> new \DateTime(), 'reviewed_by'=>$user_id]);
	}

  public function save_outcome($params){
		if( array_key_exists("outcome_authors",$params) ){
			$authors = $params['outcome_authors'];
			unset($params['outcome_authors']);
		}
		if( array_key_exists("files",$params) ){
			$files = $params['files'];
			unset($params['files']);
		}
		if(array_key_exists('id',$params)){
			$o = $this->model->find($params['id']);
			$o->update($params);
		}else{
			$o = $this->model->create($params);
		}

		if($authors){
			\App\Models\OutcomeAuthor::where(['outcome_id'=> $o->id ])->update(['status'=>0]);
			foreach( $authors as $author_data ){
				$author = \App\Models\OutcomeAuthor::where(['outcome_id'=> $o->id, 'author_id'=>$author_data['id']])->first();
				if( $author ) $author->update(['status'=>1,'role'=>$author_data['role']]);
				else{
					\App\Models\OutcomeAuthor::create(['outcome_id'=> $o->id, 'author_id'=>$author_data['id'],'role'=>$author_data['role']]);
				}
			}
		}
		return $o;
	}
	
	public function delete($id){
		\App\Models\Outcome::find($id)->update(['status'=>0]);
	}


	public function rUnitWord($phpWord, $section, $list, $params){
		$section->getStyle()->setOrientation('landscape');
		$cellHCentered = array('align' => 'center');
		$cellVCentered = array('valign' => 'center', 'bgColor' =>'FAFAFA');
		$cellRowContinue = array('vMerge' => 'continue');
		$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
		$cellColSpanP = array('gridSpan' => 3, 'valign' => 'center', 'bgColor' =>'333333');
		$cellColSpan = array('gridSpan' => 6, 'valign' => 'center', 'bgColor' =>'333333');
		$cellColSpan2 = array('gridSpan' => 6, 'valign' => 'center', 'bgColor' =>'EEEEEE');

		$section->addText(strtoupper("$params[faculty_name]"),['bold'=>true,'size'=>13]);
		$section->addText("");
		$section->addText("Informe de investigaciones  del $params[period_type] $params[period] - $params[year]");
		$section->addText("");
		$section->addText("Numero de Investigaciones: ". $params['total'] );
		$section->addText("");

		$tableP = $section->addTable();
		$tableP->addRow(400);
		$tableP->addCell(9000,$cellColSpanP)->addText("Participantes:",['color'=>'white'],['alignment'=>'center']);
		$tableP->addRow(300);
		$tableP->addCell(5000,$cellVCentered)->addText( "Docentes (".count($params['professors']).")", ['bold'=>true, 'size'=>9]);
		$tableP->addCell(5000,$cellVCentered)->addText( "Administrativos (".count($params['workers']).")", ['bold'=>true, 'size'=>9]);
		$tableP->addCell(5000,$cellVCentered)->addText( "Estudiantes (".count($params['students']).")", ['bold'=>true, 'size'=>9]);
		$tableP->addRow();
		$textrunD=$tableP->addCell()->addTextRun();
		foreach ($params['professors'] as $i => $a) {
			$textrunD->addText("$a[name]", ['size'=>9],['lineHeight'=>1.5] );$textrunD->addTextBreak();
		}
		$textrunA=$tableP->addCell()->addTextRun();
		foreach ($params['workers'] as $i => $a) {
			$textrunA->addText("$a[name]", ['size'=>9],['lineHeight'=>1.5] );$textrunA->addTextBreak();
		}
		$textrunE=$tableP->addCell()->addTextRun();
		foreach ($params['students'] as $i => $a) {
			$textrunE->addText("$a[name] ($a[dni])" , ['size'=>9],['lineHeight'=>1.5] );$textrunE->addTextBreak();
		}

		$section->addText("");
		$tableT = $section->addTable();
		$tableT->addRow(400);
		$tableT->addCell(12000,$cellColSpan)->addText("Trabajos:",['color'=>'white'],['alignment'=>'center']);
		foreach ($list as $research_by_line) {
			$table = $section->addTable();
			$table->addRow(400);
			$table->addCell(9000,$cellColSpan2)->addText("Grupo: ".$research_by_line['line']);
			$table->addRow(300);
			$table->addCell(6000,$cellVCentered)->addText( 'Titulo', ['bold'=>true, 'size'=>9]);
			$table->addCell(3000,$cellVCentered)->addText( 'Autores', ['bold'=>true, 'size'=>9]);
			$table->addCell(1000,$cellVCentered)->addText( 'Tipo', ['bold'=>true, 'size'=>9],['alignment'=>'center']);
			$table->addCell(1000,$cellVCentered)->addText( 'Inicio-Fin', ['bold'=>true, 'size'=>9],['alignment'=>'center']);
			$table->addCell(1000,$cellVCentered)->addText( 'Documento', ['bold'=>true, 'size'=>9],['alignment'=>'center']);
			$table->addCell(1800,$cellVCentered)->addText( 'Obs.', ['bold'=>true, 'size'=>9],['alignment'=>'center']);
			foreach($research_by_line['research']  as $i => $r){
				$table->addRow();
				$table->addCell()->addText($r["name"] );
				$textrun=$table->addCell()->addTextRun();
				foreach($r['authors'] as $a){
					$textrun->addText("$a[name]" .($a['role']=='Titular'?" (T)":($a['role'] =='Asesor'?' (A)':'')  ), ['size'=>9] );$textrun->addTextBreak();
				}
				$table->addCell(800)->addText( $r['type_research'],  ['size'=>9] ,['alignment'=>'center']);
				$table->addCell(1100)->addText( "$r[date_init] $r[date_end]",  ['size'=>9],['alignment'=>'center'] );
				$table->addCell(800)->addText( $r['document'],  ['size'=>8] ,['alignment'=>'center']);
				$table->addCell(800)->addText( $r['outcome'],  ['size'=>10] ,['alignment'=>'center']);
			}
		}
	}

	public function incentiveWord($phpWord, $section, $list, $params){
		$cellColSpan = ['gridSpan' => 3];
		//dd("ok");
		$section->addText("REPORTE DE INCENTIVOS ".($params['incentive_type']=='IC'?'DE INICIACIÓN CIENTÍFICA':'FEDU'). " DEL".($params['period_type']=='T'?' TRIMESTRE ':' MES '). strtoupper($params['period']). ' - '.$params['year'], ['bold'=>true],[ 'alignment'=>'center']);
		$section->addText("Total:".$params['total']." registros.");
		$section->addText(" ");
		$i=0;
		if(count($list)>0  )  $table = $section->addTable();
		foreach($list as $o){
			$table->addRow();
			//$table->addCell()->addText($o["name"],['bold'=>true])->setGridSpan(3);
			//$table->addCell()->addText();

			 $table->addCell(8000, $cellColSpan)->addText($o["name"],['bold'=>true]);
			//dd($o["name"]);
			//$cell->getStyle()->setGridSpan(3);
			//$i=0;
			foreach( $o["authors"] as $author ){
				//dd($author->dni);
				$i++;
				$table->addRow();
			$table->addCell()->addText($i );
			$table->addCell()->addText($author->author);
			$table->addCell()->addText( strpos($o["name"], "esistas" )>0 ||strpos($o["name"], "studiante" )>0 ?"DNI:  $author->dni":'');
			//$table->addCell()->addText('DNI ' . $author->dni);
			}
		}
		

	}

public function byOutWord($phpWord, $section, $data, $params){
			$section->addText("	Ejemplares publicados desde: ".$params['from']." hasta ".$params['to'],['bold'=>true]);
			$section->addText();
			$table = $section->addTable();
			$table->addRow();
			$table->addCell(2000)->addText( ' TITULO');
			$table->addCell(2000)->addText( 'AUTORES');
			$table->addCell(1200)->addText( 'TIPO');
			$table->addCell(1300)->addText( 'INDEXADO');
			$table->addCell(800)->addText( 'FECHA');
			$i = 0;
			foreach($data as $r){
				$table->addRow();
				$table->addCell(5000)->addText($r['name']);
				$textrun=$table->addCell()->addTextRun();
				foreach($r['authors'] as $a){
					$textrun->addText("$a[name]" .($a['role']=='Titular'?" (T)":""  ), ['size'=>9] );$textrun->addTextBreak();
				}
				$table->addCell(3000)->addText($r['type']);
				$table->addCell(3000)->addText($r['indexed']);
				$table->addCell(3000)->addText($r['date']);
			}
		
		}
}