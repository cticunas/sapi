<?php

use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{

    public function resolveFullName($lastname, $name){

        if( "$lastname $name"=="Chiguala Contreras Lincoln Aristoteles" ) $name="Lincoln";
        else if( "$lastname $name"=="Yujra Ccuno Victor Raul" ) $name="Victor";
        else if( "$lastname $name"=="Vasquez Pinedo Eudolio Gregorio" ) $name="Gregorio";
        else if( "$lastname $name"=="Rodriguez Delgado Segundo Clemente" ) $name="Segundo";
        else if( "$lastname $name"=="Rimarachin Valderrama Edgar Willy" ) $name="Edgar Wily";
        else if( "$lastname $name"=="Lopez Villanueva Antonio Emel" ) $name="Emel";
        else if( "$lastname $name"=="Villaizan Y Huerto Jorge Luis" ) $name="Jorge";
        else if( "$lastname $name"=="Pajuelo Maguiña Carmela Mercedes" ) $name="Carmela M.";
        else if( "$lastname $name"=="Horna Carranza Digna Efigenia" ) $name="Digna";
        else if( "$lastname $name"=="Gonzalez Manrique De Lara Tito Felipe" ) $lastname="Gonzales Manrique De Lara";
        else if( "$lastname $name"=="Atalaya Horna Cesar Emiliano" ) $name="Cesar";
        else if( "$lastname $name"=="Alegria Herrera Danmer Elber" ) $name="Danmer";
        else if( "$lastname $name"=="Turpo Calcina Jorge Suplicio" ) $name="Jorge";
        else if( "$lastname $name"=="Tafur Zevallos Lisandro Roger" ) $name="Lisandro";
        else if( "$lastname $name"=="Paredes Orellana Walter Alberto" ) $name="Walter";
        else if( "$lastname $name"=="Jurado Baquerizo Tulio Edgar" ) $name="Tulio";
        else if( "$lastname $name"=="Reyes Ayala Yessica Raquel" ) $name="Jessica";
        else if( "$lastname $name"=="Escobar Romero Leonardo Yvan" ) $name="Leonardo Ivan";
        else if( "$lastname $name"=="Bermudez Pino Wilmer Julio" ) $name="Wilmer";
        else if( "$lastname $name"=="Pocomucha Poma Vicente Serapio" ) $name="Vicente";
        else if( "$lastname $name"=="Vega Ventocilla Edwin" ) $name="Edwin Jesus";
        else if( "$lastname $name"=="Trujillo Natividad Pedro Crisologo" ) $name="Pedro";
        else if( "$lastname $name"=="Pando Soto Brian Cesar" ) $name="Brian";
        else if( "$lastname $name"=="Marchand Niño William Rogelio" ) $name="William";
        else if( "$lastname $name"=="Canales Aguirre Marco Arturo" ) $name="Marco";
        else if( "$lastname $name"=="Bernuy Blanco Walter Ruben" ) $name="Walter";
        else if( "$lastname $name"=="Levano Crisostomo Jose Dolores" ) $name="Jose";
        else if( "$lastname $name"=="Zegarra Aliaga Olimber Oscar" ) $name="Olimber";
        else if( "$lastname $name"=="Suarez Gonzales Jose Narciso" ) $name="Jose";
        else if( "$lastname $name"=="Pacheco Villena Arcenio Otilio" ) $name="Arcenio";
        else if( "$lastname $name"=="Morales Y Chocano Luis Abanto" ) $name="Luis";
        else if( "$lastname $name"=="Lazo Calle Antonio Jesus" ) $name="Antonio";
        else if( "$lastname $name"=="Fuertes Arroyo Maria Eulalia" ) $name="Maria";
        else if( "$lastname $name"=="Esteban Barzola Varely Abraham" ) $name="Varely";
        else if( "$lastname $name"=="Aguilar Guizado Jhon Kenett" ) $name="Kenet";
        else if( "$lastname $name"=="Montero Vilchez Eladio Dionisio" ) $name="Eladio";
        else if( "$lastname $name"=="Manrique Ramos Miguel Angel" ) $name="Miguel";
        else if( "$lastname $name"=="Malpartida Marquez Orlando Everardo" ) $name="Orlando";
        else if( "$lastname $name"=="Ferrer Tarazona Royer Santelle" ) $name="Royer"; //tiene 2 persona
        else if( "$lastname $name"=="Morillo Alva Mariela Luz" ) $name="Mariela";
        else if( "$lastname $name"=="Falcon Tarazona Eva Doris" ) $name="Eva";
        else if( "$lastname $name"=="Rivas Pulache  Victorino" ) $name="Victorino";
        else if( "$lastname $name"=="Miranda Armas Carlos Miguel" ) $name="Carlos";
        else if( "$lastname $name"=="Mansilla Minaya Luis German" ) $name="Luis";
        else if( "$lastname $name"=="Lechuga Pardo Luis Eduardo" ) $name="Luis";
        else if( "$lastname $name"=="Garcia Carrion Luis Fernando" ) $name="Luis";
        else if( "$lastname $name"=="Egoavil Jump Giannfranco" ) $name="Gianfranco";
        else if( "$lastname $name"=="Anteparra Paredes Miguel Eduardo" ) $name="Miguel E.";
        else if( "$lastname $name"=="Simeon Nuñez Antonio Santos" ) $name="Antonio";
        else if( "$lastname $name"=="Salazar Rojas Inocente Feliciano" ) $name="Inocente";
        else if( "$lastname $name"=="Peña Camarena Jaime Juan" ) $name="Jaime";
        else if( "$lastname $name"=="Paz Soldan Chavez Juan Dionicio" ) $name="Juan";
        else if( "$lastname $name"=="Huaman Ramirez Cesar Augusto" ) $name="Cesar A.";
        else if( "$lastname $name"=="Zavaleta De La Cruz Lauriano Antonio" ) $name="Lauriano";
        else if( "$lastname $name"=="Vejarano Jara Pedro Alejandro" ) $name="Pedro";
        else if( "$lastname $name"=="Ordoñez Gomez Elizabeth Susana" ) $name="Elizabeth";
        else if( "$lastname $name"=="Natividad Ferrer Raul Edgardo" ) $name="Raul";
        else if( "$lastname $name"=="Matos Bustamante Raida Lourdes" ) $name="Raida";
        else if( "$lastname $name"=="Giraldo Huayta Julio Constantino" ) $name="Julio";
        else if( "$lastname $name"=="Condori Rondan  Victor Elvis" ) $name="Victor Elvis";
        else if( "$lastname $name"=="Caceres Almenara Eduardo Alejandro" ) $name="Eduardo";
        else if( "$lastname $name"=="Zuñiga Cernades Luis Benigno" ) $name="Luis";
        return ["lastname"=>$lastname, "name"=>$name];
    }
    private function  bulk($csvFile,$type){
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
                if (!$firstline) {

                    if($type=='D'){   //docentes 
                        $dni=$data[0];
                        $patern=trim($data[1]);
                        $matern=trim($data[2]);
                        $lastname=trim($patern)." ".trim($matern);
                        $nombres=$data[3];
                        $category=$data[4];
                        $department=$data[5];

                        $school=trim("Escuela profesional de ".$department);
                        $condition=$data[6];
                        if($condition=='NOMBRADO') $condition='N';
                        else $condition='C'; 
                        $email = $data[7];
                        $biography=$category.",adscrito a la escuela profesional de ".$department;
                        
                        $d = $this->resolveFullName($lastname, $nombres);
                        $lastname = $d['lastname'];
                        $nombres = $d['name'];
                        /* Buscar Parecidos */
                       if(  ($whitespace = strpos($d['name'], " ")  ) >0 ){ // 2 noms.
                           $name_ = explode(" ",$d['name']); 
                           $firstname = $name_[0];
                           $secondname = $name_[1];
                           $same_persons = \App\Models\Person::whereRaw("lastname= '$lastname' and name = '$firstname' ")->get();
                           foreach( $same_persons as $p_ ){ echo "+ Parecido: $lastname $firstname\n"; }
                       }
                       /* ----- */
                       
                        $person= \App\Models\Person::where(['lastname'=>$lastname, 'name'=>$nombres])->first();
                        if(!$person) {
                            //echo ("=>Persona ($type) ($condition) no existe:".$lastname."-".$nombres."\n");
                        }
                        else{
                            $school=str_ireplace("Escuela profesional de Escuela Profesional","Escuela profesional",$school);
                            if( $school=='Escuela profesional de Ciencia y Tecnologia de Ingenieria de Alimentos'  ||  $school=='Escuela profesional de Ciencia y Tecnologa de Ingeniera de Alimentos' || $school=='Escuela profesional de Ingeniera Industrias Alimentarias' || $school=='Escuela profesional de Ciencia y Tecnologa de Ingeniera de Alimentos' || $school=='Escuela profesional de Ingenieria Industrias Alimentarias')
                                $school="Escuela Profesional de Ciencias en Tecnologia e Ingenieria de Alimentos";
                            else if ($school=="Escuela profesional de Ingenieria Mecanica Electrica" || $school=='Escuela profesional de Ingenieria Mecanica Elctrica')
                                $school = "Escuela Profesional en Mecanica Electrica";
                            else if($school=="Escuela profesional de Ciencias en Recursos Naturales Renovables")
                                $school = "Escuela Profesional de Recursos Naturales Renovables";
                            else if($school=="Escuela profesional de Ingeniera Industrias Alimentarias")
                                $school = "Escuela Profesional de Ciencias en Tecnologia e Ingenieria de Alimentos";
              
                            $college= \App\Models\Organization::whereRaw( "lower(name)='".strtolower($school)."'" )->first();


                            \App\Models\Person::where("id",$person->id)->update(['email'=>$email,'biography'=>$biography,'condition'=>$condition]); 
                            if(!$college) {
                                echo (".Escuela ($type) no existe en:".$school."\n");
                            }
                          
                        }
                    }    
                    else{
                        $dni=$data[0];
                        $patern=$data[1];
                        $matern=$data[2];
                        $lastname=$patern." ".$matern;
                        $name=$data[3];
                        $department=$data[5];
                        $condition=$data[6];
                        $email = $data[7];
                        $biography="";
                        if(!$department) { $organization=32; }
                        else{
                            $faculty= \App\Models\Organization::whereRaw( "lower(name)= '".strtolower($department)."'")->first();
                            if(!$faculty) {
                                echo ("---Escuela no existe---- ".$department."\n");
                                $organization=32;
                             } else{ $organization=$faculty->parent_id; }
                        }
                        if($type=='E'){ 
                            $type='D';
                            $biography="Docente adscrito a la ".$department;
                        }
                        $person= \App\Models\Person::where(['lastname'=>trim($lastname),'name'=>trim($name)])->first();
                        if(!$person) {
                            \App\Models\Person::create([
                                "dni"=>$data[0],
                                "lastname" => trim($lastname),
                                "name" => trim($name),
                                "condition" => $data['4'],
                                'organization_id'=>$organization,
                                "type"=> $type,
                                "email"=>$email,
                                "biography"=>$biography,
                            ]);  
                         }
                         else{
                            $person->email=$email;
                            $person->update(); 
                        }   
                    }
                }
                $firstline = false;
            }
    }
    
    
    public function run()
    {
        $csvFile = fopen(base_path("database/data/dataCorreosDocentes.csv"), "r");
        $this->bulk($csvFile,'D');
        fclose($csvFile);
       $csvFile = fopen(base_path("database/data/dataDocentesContratados.csv"), "r");
        $this->bulk($csvFile,'E');
        fclose($csvFile);
        $csvFile = fopen(base_path("database/data/dataFuncionarios.csv"), "r");
        $this->bulk($csvFile,'A');
        fclose($csvFile);
    }
}