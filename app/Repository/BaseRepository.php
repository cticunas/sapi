<?php

namespace App\Repository;

use Egulias\EmailValidator\Exception\AtextAfterCFWS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Style\Language;

class BaseRepository {
    protected $model;
    public function __construct(Model $model){
        $this->model = $model;
    }

    // public function all()
    // {
    //     return $this->model->all();
    // }

    public function create(array $attributes): Model{
        return $this->model->create($attributes);
    }

    public function update(array $data, $id)
    {
        return $this->model->where('id', $id)
            ->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function find($id): ?Model{
        return $this->model->find($id);
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

    public function add_condition($field, $params, $table_prefix=false){
		if( array_key_exists($field, $params) ) {
			$condition = [($table_prefix ? "$table_prefix." : '').$field  ,'=', $params[$field]];
		} else { throw new Exception("$field not found in params"); }
		return $condition;
	}

    public function getPDODriver(){
        return DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    public function getDatePart($period_type, $date_field){
        if($this->getPDODriver()  =='pgsql'){
            $func= ($period_type=='M'?'extract(month from':'extract(quarter from') ." $date_field)";
        }else{
            $func= ($period_type=='M'?'month':'quarter') ."($date_field)";
        }
        return $func;
    }

    public function getDateFormatSql($field, $format=false ){
        $driver = $this->getPDODriver();
        $pg_format= "YYYY-MM-DD";
        $my_format="%Y-%m-%d";
        if($format=='d/m/Y' && $driver=='pgsql') $pg_format="DD/MM/YYYY";
        else if($format=='d/m/Y' &&  $driver=='mysql') $my_format="%d/%m/%Y";
        return  $driver=='pgsql'? "to_char($field, '$pg_format' )":"date_format($field,' $my_format')";
    }

    public function getYearSqlPart($dateField){
        return  $this->getPDODriver()  =='pgsql'? "DATE_PART('year',$dateField)":"YEAR($dateField)";
    }

    public function getOutcomeAuthorsSqlPart($author_full=false){
        $author_fields = $author_full?"author_id,'|',p.name,' ',p.lastname, '|', role,'|',p.photo, '|', p.type, '|', COALESCE(p.condition,'-'), '|',  COALESCE(p.dni,'-')":"author_id,'|',p.name,' ',p.lastname, '|', role,'|',p.photo";

        if(  $this->getPDODriver() =='pgsql'){
            $sql= "(select array_to_string(array_agg( concat($author_fields) ),',') from outcome_authors oa  inner join people p on p.id=oa.author_id
            where outcomes.id=oa.outcome_id and oa.status=1 group by oa.outcome_id)";
        }else
            $sql = "(select group_concat( $author_fields ) from outcome_authors oa inner join people p on p.id=oa.author_id   where outcomes.id=oa.outcome_id and oa.status=1 group by oa.outcome_id)";
        return $sql;
    }

    public function getGroupConcatSqlPart($fields){
        return $this->getPDODriver() =='pgsql'?" array_to_string(array_agg( concat($fields) ),',') ": "group_concat($fields)";
    }

    public function getResearchAuthorsSqlPart($inc_lastname = false) {
        $fields = $inc_lastname?"author_id,'|',p.name,'|',p.lastname,'|', role,'|',p.photo":"author_id,'|',p.name,' ',p.lastname, '|', role,'|',p.photo";
        if( $this->getPDODriver() == 'pgsql'){
            $sql = "(select array_to_string(array_agg( concat($fields ) ),',')
            from  research_authors ra inner join people p on p.id=ra.author_id
            where research_id=research.id and ra.status=1  group by ra.research_id)";
        } else
            $sql = "(select group_concat( $fields  ) from research_authors ra
			inner join people p on p.id=ra.author_id where research_id=research.id and ra.status=1  group by ra.research_id)";
            return $sql;
    }

    public function saveWord($filename, $phpWord){
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try { $objWriter->save(storage_path($filename)); }
        catch (Exception $e) { }
    }

    public function wordTemplate($orientation="portrait", $faculty_name=false ){
        $university="UNIVERSIDAD NACIONAL AGRARIA DE LA SELVA";
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->addParagraphStyle('p_header', array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
		$phpWord->addParagraphStyle('t_header', ['size' => 13, 'bold' => true]);
		$section = $phpWord->addSection();
		$header = $section->addHeader();
        $styleTable = array('borderBottomSize' => 10, 'borderBottomColor' => '999999','cellMarginBottom'=>10);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        // $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan = array( 'valign' => 'center');
        $cellHCentered = array('align' => 'center', 'lineHeight'=>1.1);
        $cellVCentered = array('valign' => 'center');
        if( $faculty_name ) {
            $header->addText($university,null,['alignment'=>'center']);
            $header->addText($faculty_name,null,['alignment'=>'center']);
            $lineStyle = array('weight' => 1, 'width' => 450, 'height' => 0, 'color' => 'black');
            $header->addLine($lineStyle);
        }else{
            $phpWord->addTableStyle('Colspan_Rowspan', $styleTable);
            $table = $header->addTable('Colspan_Rowspan');
            $table->addRow();
            $table->addCell(1000, $cellRowSpan)->addImage( 'images/logounas.png', [ 'width' => 50,'height' => 50]);
            $table->addCell($orientation=='portrait'?7000:12000, $cellColSpan)->addText($university, ['bold'=>true,'size'=>'12' ], $cellHCentered);
            $table->addCell(1000, $cellRowSpan)->addImage( 'images/logo_oiunas.jpg', [ 'width' => 50,'height' => 50]);
            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(6000, $cellVCentered)->addText('Tingo María', ['size'=>'11' ], $cellHCentered);
            $table->addCell(null, $cellRowContinue);
            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(7000, $cellVCentered)->addText('UNIDAD DE GESTIÓN DE LA INVESTIGACIÓN', ['size'=>'12' ], $cellHCentered);
            $table->addCell(null, $cellRowContinue);
            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(7000, $cellVCentered)->addText('"Promoviendo la Calidad de la Investigación"', ['italic'=>true,'size'=>'10' ], $cellHCentered);
            $table->addCell(null, $cellRowContinue);
        }


		$header->addText(" ");
		return ['phpWord'=>$phpWord, 'section' => $section ];
	}
}
