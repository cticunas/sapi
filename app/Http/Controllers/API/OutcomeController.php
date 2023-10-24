<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\OutcomeRepositoryI;
use App\Repository\ResearchRepositoryI;
use Illuminate\Support\Facades\View;


class OutcomeController extends Controller
{
    private $repository;
    function __construct( OutcomeRepositoryI $repository, ResearchRepositoryI $researchRepository ){
        $this->repository = $repository;
        $this->researchRepository=$researchRepository;
    }

    function index(Request $request){
        $data=$request->all();
        $list=$this->repository->all($data);
        return $list;
    }

    public function incentives(Request $request){

        $params=$request->all();
        // $person_type=$params['person_type'];
        // $person_type = $person_type=='D'?'Docente':($person_type=='A'?'Administrativo': ($person_type=='E'?'Estudiante':'Otro') );
        $data=[];
        $total = 0;
        if( $params['incentive_type'] =='IC' ){
            $params["type_research"]=2;
            $students = $this->repository->scientific_initiation($params);
            $total = count($students); 
            $s_organizations= array_unique(array_map(function($e){return $e->organization;},$students->toArray()));
            foreach($s_organizations as $o){
                $authors= array_filter($students->toArray(), function($e) use($o) { return $e->organization == $o; });
                $data[]=["name"=>"Estudiantes de la ".$o,"authors"=>$authors];
            }
        }else{
            $params['person_type']='E';
            $params['type_research']=1;
    
            $students = $this->repository->investigations_by_trimester($params);
            $total = count($students); 
            $params['person_type']='D';
            $params['type_research']=2;
            $params['condition']='N';
            $professors = $this->repository->investigations_by_trimester($params);
            $total += count($professors); 
            $params['person_type']='A';
            $params['type_research']=2;
            $params['condition']='N';
            $workers = $this->repository->investigations_by_trimester($params);
            $total += count($workers); 

            $p_organizations= array_unique(array_map(function($e){return $e->organization;},$professors->toArray()));
            $s_organizations= array_unique(array_map(function($e){return $e->organization;},$students->toArray()));
            $w_organizations= array_unique(array_map(function($e){return $e->organization;},$workers->toArray()));
            $data=[];
            foreach($p_organizations as $o){
                $authors= array_filter($professors->toArray(), function($e) use($o) { return $e->organization == $o; });
                $data[]=["name"=>"Docentes de la ".$o,"authors"=>$authors];
            }
            foreach($s_organizations as $o){
                $authors= array_filter($students->toArray(), function($e) use($o) { return $e->organization == $o; });
                $data[]=["name"=>"Tesistas de la ".$o,"authors"=>$authors];
            }
            foreach($w_organizations as $o){
                $authors= array_filter($workers->toArray(), function($e) use($o) { return $e->organization == $o; });
                $data[]=["name"=>"Administrativos de la ".$o,"authors"=>$authors];
            }

           //dd($data);
        }

        $filename = $params['incentive_type']=='IC'?'Iniciacion_cientifica':'FEDU';
        
        if( $params['format']=='word'){
            $word = $this->repository->wordTemplate(); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            // $params['person_type'] = $person_type;
            $params['total']=$total;
            
            $this->repository->incentiveWord($phpWord, $section , $data, $params);
             $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            
             try { $objWriter->save(storage_path("$filename.docx")); } catch (Exception $e) { }
             return response()->download(storage_path("$filename.docx"));
        }else{
        $pdf = \PDF::loadView('reports/incentives_pdf', ['data'=>$data, 'period_type'=>$params['period_type'], 'period'=>$params['period'],'year'=>$params['year'],'i'=>1, 'incentive_type'=>$params['incentive_type'] ,'total'=>$total ] );
        return $pdf->stream("$filename.pdf");
       }
    }

    public function out_by(Request $request){
        $params=$request->all();
        $params['type']=4;
        $list=$this->repository->out_by($params);
        $research = array_map(function($e){
            $authors = $this->researchRepository->explode_authors($e->authors,false);
            $type = $e->type==4?'Artículo':($e->type==5?'Libro':'-');
            return ["name"=>$e->name, 'type'=>$type, 'indexed'=>$e->indexed, 'date'=>$e->date , "authors"=>$authors];
        },$list->toArray());
        $data = $research;
        if( $params['format']=='word'){
            $filename= 'exemplar.docx';
            $word = $this->repository->wordTemplate(); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            $i=1;
            $params['i']=$i;
          
            $this->repository->byOutWord($phpWord, $section , $data, $params);
             $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
             try { $objWriter->save(storage_path($filename)); } catch (Exception $e) { }
             return response()->download(storage_path($filename));
            //dd($view_content); 
        }
       else{
        $pdf = \PDF::loadView('reports/out_by_pdf', ['data'=>$data, 'from'=>$params['from'], 'to'=>$params['to']] );
        return $pdf->stream('ejemplares.pdf');   
       }  
    }

    public function r_for_unit(Request $request){

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $params=$request->all();
        $params['approved']= 1;
        //$params['incentive']= 1;
        $params['external']= 0;
        $params['xpage']= 100;
        $faculty = \App\Models\Organization::find($params['faculty_id']);
        $params['include_groups']=true;
        $abbreviation=$faculty->abbreviation;
        $params['author_full'] = true;
        $list=$this->repository->all($params);
        $total = count($list);

        $months=['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
        $month=$months[(int)date('m')];
        $now=date('d')." de ".$months[(int)date('m')]." de ".date('Y');
        $list =  $list->toArray()['data'] ;
        $types = ['-','Proyecto','Avance','Inf. Final','Articulo','Libro','-'];
        $r_types = ['-','Tesis','I. Docente','Experiencia','Innovacion','-'];
        $people = [];
        $data = [];
        for($i=0; $i< count($list); $i++ ){
            $e = $list[$i];
            $outcome = $types[$e->type];
            $authors = $this->researchRepository->explode_authors($e->authors, false, true, true, true,true);
            //dd( (new \Datetime($e->research_date_init))->format("d/m/Y") );
            $data[]= [
                "name"=>$e->name, 
                'type_research'=> $r_types[$e->type_research],
                'date_init'=>$e->research_date_init,
                'date_end'=>$e->research_date_end,
                'document'=>$e->document, "authors"=>$authors,
                'outcome'=> $outcome,
                'group_id'=>$e->group_id,
                "group"=>$e->group
            ];

            if(  $e->type_research ==1 ) {
               $authors= array_filter($authors, function($e){ return $e['type']=='E' ; });
            }
            $people= array_merge($people, $authors);
        }
        
        $period_type = $params['period_type']=='M'?'Mes': ($params['period_type']=='T'?'Trimestre':'-');
      
        $people= array_map('unserialize', array_unique(array_map('serialize', $people)));
        $professors = array_filter($people, function($e){ return $e['type']=='D' && $e['condition'] == 'N' ; });
        $students = array_filter($people, function($e){ return $e['type']=='E' ; });
        $workers = array_filter($people, function($e){ return $e['type']=='A' && $e['condition'] == 'N' ; });

         $lines_id = array_values(array_unique(array_map(function($e){ return $e['group_id']; }, $data)));
         $research_by_lines = []; 
        
         for ($i=0; $i < count($lines_id) ; $i++) { 
             $line = $lines_id[$i];
             $research =  array_values( array_filter($data, function($e) use ($line){ return $e['group_id'] ==$line;  }) );
             $line_name = $research[ 0 ]['group'];
             $research_by_lines[] = ['line' => $line_name,'research'=> $research ];
         }
       
        if( $params['format']=='word'){
            $filename= 'informe_unidad.docx';
            $word = $this->repository->wordTemplate( 'landscape',  $faculty->name); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            $data = $research_by_lines;
           
          $params=[
            'faculty_name'=>$faculty->name,'abbreviation'=>$abbreviation, 'period'=> $params['period'], 'period_type'=>  $period_type, 'year'=>$params['year'] ,'professors'=>$professors, 'students'=>$students, 'workers'=>$workers, 'now'=>$now,'month'=>$month,
            'total'=>$total
          ];
            $this->repository->rUnitWord($phpWord, $section , $data, $params);
             $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
             try { $objWriter->save(storage_path($filename)); } catch (Exception $e) { }
             return response()->download(storage_path($filename));
        }else{
            $pdf = \PDF::loadView('reports/out_for_unit', ['data'=>$research_by_lines, 'faculty_name'=>$faculty->name,'abbreviation'=>$abbreviation, 'period'=> $params['period'], 'period_type'=>  $period_type, 'year'=>$params['year'] ,'professors'=>$professors, 'students'=>$students, 'workers'=>$workers, 'now'=>$now,'month'=>$month ,'format'=> $params['format'] , 'total'=>$total  ] );
            return $pdf->setPaper('a4', 'landscape')->stream('report_unidad.pdf');
       }
       \PhpOffice\PhpWord\Shared\Html::addHtml($section, $view_content , false, false);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(storage_path('reporte_unidad.docx'));
        } catch (Exception $e) {
        }
        return response()->download(storage_path('reporte_unidad.docx'));
    }
    
    function save_pending_outcomes(Request $request){
        $data=$request->all();
        return $this->repository->save_pending_outcomes($data);
    }

    function save_reviewed_pending_outcomes(Request $request){
        $data=$request->all();
        return $this->repository->save_reviewed_pending_outcomes($data);
    }

    function store(Request $request){
        $data=$request->all();
       return $this->repository->save_outcome($data);
    }

    function update($id, Request $request){
        $data=$request->all();
        $data['id'] = $id;
        return $this->repository->save_outcome($data);
    }

    function destroy($id){
        return $this->repository->delete($id);
    }
}
