<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\ResearchRepositoryI;
use Illuminate\Support\Facades\View;

class ResearchController extends Controller
{
    private $repository;
    private $types=['-','Tesis','Investigacion Docente','Proyecto Experiencia','Proyecto Innovacion'];
    function __construct(ResearchRepositoryI $repository){
        $this->repository = $repository;
    }

    public function index(Request $request){
        $params=$request->all();
        $list=$this->repository->all($params);
        return $list;
    }

    public function public_list(Request $request){
        $params=$request->all();
        $list=$this->repository->public_list($params);
        return $list;
    }
    public function public_list_by_year(Request $request){
        $params=$request->all();
        $params["group_by"]="year";
        $list=$this->repository->public_list($params);
        $years=array_map(function($e){return $e->year;},$list->toArray());
        $years=array_unique($years);
        $data=[];
        foreach ($years as $year){
            $total=array_reduce($list->toArray(),function($s,$e) use($year){ $s=$s+( $e->year==$year?$e->total:0 ); return $s;  },0);
            $data[]=['year'=>$year,'total'=>$total ];
        }
        return $data;
    }

    public function py_by_collage(Request $request){
        $params=$request->all();
        $list=$this->repository->py_by($params);
        $research = array_map(function($e){ 
            
            $authors = $this->repository->explode_authors($e->authors);
            
            return ["code"=>$e->code,"name"=>$e->name,"lastname"=>$e->lastname, "title"=>$e->title, "type"=>$e->type, "year"=>$e->year,"document"=>$e->document, "authors"=>$authors,"research_state"=>$e->research_state];
        },$list->toArray());
        
        $pdf = \PDF::loadView('reports/py_by_collage_pdf', ['data'=>$research,'grade'=>$params['grade'],'type_research'=>$params['type_research'],'organization_id'=>$params['organization_id'],'i'=>1]);
        return $pdf->stream('py_by_college.pdf');        
    }

    public function py_by_state(Request $request){
        $params=$request->all();
        $list=$this->repository->py_by($params);
        $research = array_map(function($e){ 
            $authors = $this->repository->explode_authors($e->authors,false);
            return ["code"=>$e->code,"title"=>$e->title, "document"=>$e->document, "authors"=>$authors,"research_state"=>$e->research_state];
        },$list->toArray());
        //dd($research);
        if( $params['format']=='word'){
            $filename= 'i_x_state.docx';
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            $word = $this->repository->wordTemplate(); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            $data = $research;
            $i=1;
            $params['i']=$i;
            $this->repository->byStateWord($phpWord, $section , $data, $params);
            $this->repository->saveWord($filename, $phpWord);
             return response()->download(storage_path($filename));
            //dd($view_content); 
        }
       else{
            $pdf = \PDF::loadView('reports/py_by_state_pdf', ['data'=>$research,'type_research'=>$params['type_research'],'research_state_id'=>$params['research_state_id'],'i'=>1]);
            return $pdf->stream('py_by_state.pdf');    
       }  
    }

    public function py_by_college(Request $request){
        $params=$request->all();
        $list=$this->repository->py_by($params);
        $college=\App\Models\Organization::find($params['organization_id']);
        $research = array_map(function($e){ 
            $authors = $this->repository->explode_authors($e->authors,false);
            return ["code"=>$e->code,"title"=>$e->title, "document"=>$e->document, "authors"=>$authors,"organization_id"=>$e->organization_id];
        },$list->toArray());
         if( $params['format']=='word'){
             //dd($list);
            $filename= 'i_x_college.docx';
            $word = $this->repository->wordTemplate(); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            $data = $research;
            $i=1;
            $params['i']=$i;
            $params['college']=$college;
          
            $this->repository->byCollegeWord($phpWord, $section , $data, $params);
             $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
             try { $objWriter->save(storage_path($filename)); } catch (Exception $e) { }
             return response()->download(storage_path($filename));
            //dd($view_content); 
        }
       else{
            $pdf = \PDF::loadView('reports/py_by_college_pdf', ['data'=>$research,'type_research'=>$params['type_research'],'organization_id'=>$params['organization_id'],'i'=>1,'college'=>$college]);
        return $pdf->stream('py_by_college.pdf'); 
       }        
    }

    public function py_by_author(Request $request){
        $params=$request->all();
        $grades = ['-','PREGRADO','POSGRADO'];
        $grade = array_key_exists('grade',$params)?$grades[ $params['grade'] ] : '';
        $list=$this->repository->py_by_author($params);
        $research = array_map(function($e){ 
            $authors = $this->repository->explode_authors($e->authors, false);
            return ["code"=>$e->code,'role'=>$e->role, "type_research"=>$e->type_research ,"name"=>$e->name,"lastname"=>$e->lastname, "title"=>$e->title, "type"=>$e->type, "year_init"=>$e->year_init,"year_end"=>$e->year_end,"document"=>$e->document, "authors"=>$authors,"research_state"=>$e->research_state];
        },$list->toArray());

        $params['author'] = [ 'name'=>'--', 'lastname'=>'--','role'=>'--' ];
        $params['type_research'] = '-';
        if(count( $research )>0){
            $params['author'] = [ 'name'=>$research[0]['name'], 'lastname'=>$research[0]['lastname'],'role'=>$research[0]['role']  ];
        $params['type_research'] = $research[0]['type_research']==1 && $research[0]['type']=='E' ? 'TESIS':
                    ( $research[0]['type_research']==1 && $research[0]['type']=='D' ? 'TESIS ASESORADAS':(
                      $research[0]['type_research']==2  ? 'INVESTIGACION DOCENTE':(
                        $research[0]['type_research']==3  ? 'EXPERIENCIAS PROFESIONALES':'-'
                      )
                    ) );
        }
        if($params['format']=='word'){
            $filename= 'i_x_autor.docx';
            $word = $this->repository->wordTemplate(); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
        
            $this->repository->byAuthorWord($phpWord, $section , $research, $params);
             $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
             try { $objWriter->save(storage_path($filename)); } catch (Exception $e) { }
             return response()->download(storage_path($filename));
        }else{
            $pdf = \PDF::loadView('reports/py_by_author', ['data'=>$research,'grade'=>$grade,'type_research'=>$params['type_research'],'author_id'=>$params['author_id'],'i'=>1]);
             return $pdf->stream('py_by_author.pdf');     
        }
           
    }

    public function constancy_by_author(Request $request){
        
        $params=$request->all();
        $months=['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
        $now=date('d')." de ".$months[(int)date('m')]." de ".date('Y');
        
        $author=$this->repository->get_author($params['author_id']);
        //dd($author);
        switch( $author['degree']){
            case 'M':case 'T':  $author['degree']="Mg.";break;
            case 'D': $author['degree']="Dr."; break;
            default: $author['degree']="Sr.";
        }
  
        $list=$this->repository->constancy_by_author($params);
        $research = array_map(function($e){ 
            $authors = $this->repository->explode_authors($e->authors,false);
            return ["code"=>$e->code,"title"=>$e->title,"date_init"=>$e->date_init,"date_end"=>$e->date_end, "year"=>$e->year,"document"=>$e->document, "authors"=>$authors,"research_state"=>$e->research_state];
        },$list->toArray());

        $data[]=["research"=>$research];
        if( $params['format']=='word'){
            $filename= 'constancia.docx';
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            $word = $this->repository->wordTemplate( 'portrait', $author['faculty']->name); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            $data = $research;
            $params['author'] = $author;
            $params['now'] = $now;
            $this->repository->constancyWord($phpWord, $section , $data, $params);
            $this->repository->saveWord($filename, $phpWord);
             return response()->download(storage_path($filename));
        }
       else{
           $data = ['research'=>$research,'i'=>1, 'author'=>$author,'now'=>$now ];
            return view('reports/constancy',$data);
       }  
    }

    public function certified_by_author(Request $request){
        $params=$request->all();
        $months=['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
        $now=date('d')." de ".$months[(int)date('m')]." de ".date('Y');
        $author=$this->repository->get_author($params['author_id']);
       
        switch( $author['degree']){
            case 'M':case 'T':  $author['degree']="Mg.";break;
            case 'D': $author['degree']="Dr."; break;
            default: $author['degree']="Sr.";
        }
        
        $list=$this->repository->certified_by_author($params);
        $research = array_map(function($e) use($author, $params){ 
        $authors = $this->repository->explode_authors($e->authors, 'APA');

            if($params['type_research']==2) //docente
            foreach($authors as $i=> $a){
                if(  $author['id'] != $a['id']  ){
                    $lst_ = explode(" ",$a['lastname']);
                    $lst = $lst_[0];
                     if( count($lst_)>1 ){
                        $lst .= " ". substr($lst_[1],0,1);
                    }
                    $authors[$i]['lastname'] = $lst;
                    $authors[$i]['name'] = substr($authors[$i]['name'],0,1);
                }
            }
            return ["code"=>$e->code,"title"=>$e->title, "year"=>$e->year,"date_init"=>$e->date_init,"date_end"=>$e->date_end,"document"=>$e->document, "authors"=>$authors,"research_state"=>$e->research_state];
        },$list->toArray());
        if( $params['format']=='word'){
            $filename= 'certificado.docx';
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            $word = $this->repository->wordTemplate( 'portrait', $author['faculty']->name); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            $data = $research;
            $params['author'] = $author;
            $params['now'] = $now;
            $params['role']=$params['type_research']==1?'Asesor':'Miembro Ordinario';
            if(!empty($params['grade']) ) 
            $params['grade']=$params['grade'] ==1?'PREGRADO':($params['grade'] ==2?'POSGRADO':'PREGRADO Y POSGRADO');
            $this->repository->certifiedWord($phpWord, $section , $data, $params);
            $this->repository->saveWord($filename, $phpWord);
             return response()->download(storage_path($filename));
        } else{
            $data = [
                'research'=>$research,
                'i'=>1,
                'author'=>$author,
                'now'=>$now,
                'role'=>$params['type_research']==1?'Asesor':'Miembro Ordinario'
            ];
            if( array_key_exists("grade",$params) ) 
            $data['grade']=$params['grade'] ==1?'PREGRADO':($params['grade'] ==2?'POSGRADO':'PREGRADO Y POSGRADO');
            return view('reports/certified',$data);
       }  
    }

    public function sunedu_list(Request $request){
        try {
            $params=$request->all();

            $list=$this->repository->sunedu_list($params);
          
            if($params['research_state_id']==3) $research_state="en ejecucion"; 
            else  $research_state="culminados";  
            $params['research_state_id']=$research_state;
            $colleges = array_map(function($e){ 
            return $e->college; 
          },$list->toArray() );
          //dd($list);
          $colleges = array_unique($colleges);
          $data=[];
          foreach($colleges as $c){
                $research_by_org= array_filter($list->toArray(), function($e) use($c) {
                  return $e->college == $c;
                 });
                $research = array_map(function($e){ 
                $authors_ = explode(",",$e->authors);
                //dd($authors_);
                $authors=[];
                $objectives=[];
                $main_objective=['name'=>''];
                $main_author='';
                foreach($authors_ as $a){
                     $a_ = explode("|",$a);
                     if($a_[2] == 'TI'){$main_author=["id"=>$a_[0], "name"=>$a_[1]];}
                     else{$authors[] = ["id"=>$a_[0], "name"=>$a_[1]]; }
                }
                   //dd($e->results);
                   if($e->results=="")  $results[0]="Avance(0)";
                   else{
                    $results_ = explode(",",$e->results);
                    $results=[];
                    $avs=0;
                    $arts=0;
                    $infs=0;
                    $bks=0;
                    foreach($results_ as $re){
                        $re_ = explode("|",$re);
                        switch($re_[1]){
                            case '2':$avs++; break;
                            case '3':$infs++; break;
                            case '4':$arts++; break;
                            case '5':$bks++; break;
                        }
                    }
                    $results[0]="Avance(0)";
                    $results[1] = "Avance: ($avs). Informe Trimestral"; 
                    $results[2] = "Informe Final:"; 
                    $results[3] = "Articulo Cientifico: ($arts)"; 
                    //$results[4] = "Libro ($bks)";
                }
                if($e->fin_company=="") $e->fin_company="Universidad Nacional Agraria de la Selva";
                $fin_type_ = $e->fin_type;
                switch($fin_type_){
                    case '1':$e->fin_type="Financiamiento interno"; break;
                    case '2':$e->fin_type="Financiamiento externo"; break;
                    case '3':$e->fin_type="Autofinanciado"; break;
                }
                $json_objectives=json_decode($e->objectives,true);
                //dump($json_objectives);
                if($json_objectives)
                foreach($json_objectives as $i=>$objective){
                    if($i==0){$main_objective=$objective; }
                    else{$objectives[]=$objective;}
                } 
                //dd($results);
                return ["college"=>$e->college,"main_objective"=>$main_objective,"objectives"=>$objectives,"title"=>$e->title, "date_init"=>$e->date_init, "date_end"=>$e->date_end,"budget"=>$e->budget,"fin_company"=>$e->fin_company,"line"=>$e->line,"main_author"=>$main_author,"authors"=>$authors,"fin_type"=>$e->fin_type,'results'=>$results];
            }, $research_by_org);
              $data[]=["org"=>$c,"research"=>$research,"research_state"=>$research_state];
          }
        if( $params['format']=='excel'){
            $excel=new \App\Exports\SuneduExport(['data'=>$data]);   
            return $excel->download('reporte_sunedu.xlsx',\Maatwebsite\Excel\Excel::XLSX); 
        }
       else{
            return view('reports/sunedu',['data'=>$data,'i'=>1]);
       }
        } catch (\Exception $e) {
            die($e);
        }
    }

    public function list_status(Request $request){
        $params=$request->all();
        $o=$this->repository->listStatus($params);
        return $o;
    }
    public function save_status(Request $request){
        $data=$request->all();
        $o=$this->repository->saveStatus($data);
        return $o;
    }

    public function store(Request $request){
        $data=$request->all();
        $o=$this->repository->save($data);
        return $o;
    }

    public function update($id, Request $request)
    {
        $data=$request->all();
        $data['id'] = $id;
        $o=$this->repository->save($data);
        return $o;
    }

    public function destroy($id)
    {
        return $this->repository->delete($id);
    }

    public function py_by_period(Request $request){
        $params=$request->all();
        $list=$this->repository->by_period($params);
        //dd($list->toArray());
        $research_state_id=$params['research_state_id'];
        switch($research_state_id){
            case 2: $research_state="NUEVO";break;
            case 3: $research_state="EN EJECUCION";break;
            case 4: $research_state="CULMINADO";break;
            case 5: $research_state="SUSPENDIDO";break;
            case 6: $research_state="ANULADO";break;
        }
        $params['research_state_id']=$research_state;
        $type_research=$params['type_research'];
        switch( $type_research){
            case 1: $type_research="Tesis";break;
            case 2: $type_research="Inv. Docente";break;
            case 3: $type_research="Experiencia";break;
            case 4: $type_research="Innovación";break;
        }
        $organizations = array_map(function($e){  return $e->organization; },$list->toArray() );
        $organizations = array_unique($organizations);
        $data=[];
        foreach($organizations as $o){
            $research_by_org= array_filter($list->toArray(), function($e) use($o) { return $e->organization == $o;});
            $research = array_map(function($e){ 
                $authors=$this->repository->explode_authors($e->authors, false);
                return ["code"=>$e->code,"date_init"=>$e->date_init,"date_end"=>$e->date_end ,"title"=>$e->title, "log_date_at"=>$e->log_date_at, "authors"=>$authors];
            },$research_by_org);
            
            $data[]=["org"=>$o,"research"=>$research];
            //dd($data);
        }
        if($params['format']=='word'){
            $filename= 'i_x_periodo.docx';
            $word = $this->repository->wordTemplate(); 
            $phpWord = $word['phpWord'];
            $section = $word['section'];
            $params['type_research'] =  strtoupper($this->types[$params['type_research']]);
            $this->repository->byPeriodWord($phpWord, $section , $data, $params);
             $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
             try { $objWriter->save(storage_path($filename)); } catch (Exception $e) { }
             return response()->download(storage_path($filename));
        }else{
            //dd($data);
            $pdf = \PDF::loadView('reports/py_by_period_pdf', ['data'=>$data,'period_type'=>$params['period_type'],'period'=>$params['period'],'year'=>$params['year'],'state'=>$params['research_state_id'],'research_state'=>$research_state,'type_research'=>$type_research] );
            return $pdf->stream('py_by_period.pdf');
        }
        
    }
}
