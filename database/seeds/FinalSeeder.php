<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //conseguir los personas de nombre duplicado. 
        $samePersons=\App\Models\Person::whereRaw("lastname != '' ");
       
        $db_driver = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        if( $db_driver=="pgsql" ){
            $samePersons=$samePersons->select( "lastname","name");
        }
        $samePersons= $samePersons->groupBy(DB::raw('lastname, name'))->havingRaw("count(id)>1")->get();
        foreach($samePersons as $sp){
            $persons =\App\Models\Person::where(['lastname'=>$sp->lastname, 'name'=>$sp->name ])->get()->toArray();
            $real_p =  array_values(array_filter($persons,function($e){  return $e['type']=="D";}));
    
           
            $real_p = count($real_p)>0? $real_p [0]: $persons[0];
            foreach( $persons as $p ){
                if( $p['id'] != $real_p['id'] ){
                    //poner investigaciones del primer investigador en el seggundo.
                    \App\Models\ResearchAuthor::where( ["author_id"=>$p['id']] )->update(['author_id'=>$real_p['id']]);
                   \App\Models\OutcomeAuthor::where( ["author_id"=>$p['id']] )->update(['author_id'=>$real_p['id']]);
                   //borrar el primer investigador.
                    \App\Models\Person::destroy($p['id']);
                    echo "Borrando duplicado: $p[name] $p[lastname].\n";
                }
                    
            }
        }
        $list=[
            ["Albujar Nateros Yoc - Linn","Albujar Nateros Yoc-Lin",0],
            ["Alva Valdiviezo Wilfredo","Alva Valdiviezo Jose Wilfredo",0],//Editado 
            ["Adriazola Del Aguila Jorge","Adriazola Del Aguila Jorge Luis",1],
            ["Alvarez Melo Jorge","Alvarez Melo Jorge Birino",0],
            ["Arevalo Arevalo Carlos","Arevalo Arevalo Carlos Enrique","Arevalo Arevalo Carlos E.",1],//3 Editado
            ["Arevalo Ramirez Carlos Segundo","Arevalo Ramirez Carlos",0],
            ["Ascencio Tadeo Jose Carlos","Asencio Tadeo Jose",0],
            ["Balcazar Terrones Luz","Balcazar Terrones Luz Elita",1],//Editado
            ["Bazan Rivera Jimmy","Bazan Rivera Jimmy Roshimber",1], //Editado
           ["Blas Matienzo Jose", "Blas Matienzo Jose Antonio", 1 ], //Editado
           ["Bravo Morales Nino Frank","Bravo Morales Nino",0],
           ["Cabezas Huayllas Oscar","Cabezas Huayllas Oscar Esmael",1], //Editado
           [ "Caceres Almenara Eduardo","Caceres Almenara Eduardo Alejandro", 1],//Editado
            ["Carmona Ruiz Alfredo","Carmona Ruiz Alfredo Abelardo",1],//Editado
            ["Castillo Soto Wilson","Castillo Soto Wilson Lino",0],
            ["Chuqui Paulino Franz Joel","Chuqui Paulino Franz",0],
            ["Coz Rodriguez Edward Javier","Coz Rodriguez Javier",0],
            ["Davila Honorio Duany","Davila Honorario Duany",0],
            ["Del Aguila Angulo Marianella","Del Aguila Angulo Marianela Br.",1],
            //["Del Aguila Rojas Karin Sheyla","Del Aguila Rojas Karin  Sheyla",0],
            ["Del Valle Manyari Luis","Del Valle Manyari Luis Edgardo",0],
            ["Chavez Asencio Ricardo M.","Chavez Asencio Ricardo Martin",1], //Editado
            ["Chia Wong Julio A.","Chia Wong Julio Alfonso",0],
            ["Coaguila Rodriguez Perci","Coaguila Rodriguez Perci Peter",0],
            ["Dionisio Garma Maximo","Dionisio Garma Maximo Alfredo",1],
            ["Escobar Romero Leonardo Ivan","Escobar Romero Leonardo Yvan",0],
            ["Ferrer Tarazona Royer","Ferrer Tarazona Royer S.",0],
            ["Florida Rofner Nelino","Florinda Rofner Nelino",0],
            ["Garcia Caballero Ruth Esther","Garcia Caballero Ruth",0],
            ["Gonzales Huiman Fernando","Gonzales Huiman Fernando Segundo",1],//Editado
            ["Guerra Lu Jose","Guerra Lu Jose Kalion",1],//Editado
            ["Guerrero Vejarano Tania","Guerrero Vejarano Tania Elizabeth",1],//Editado
            ["Guerrero Cabrera Jesus","Guerrero Cabrera Jesus Gaudencia",0],
            ["Guevara Yberico Victor Alfredo","Guevara Yverico Victor Alfredo",0],
             //["Gutierrez Campos Paul  Engel","Gutierrez Campos Paul Engel",1],
             ["Gutierrez Collao Jairo","Gutierrez Collao Jairo Edson",0],
             // ["Gutierrez Fernandez Osmar","Gutierrez Fernnadez Osmar ",0],
              ["Huaman Ortega Melida Karina","Huaman Ortega Karina",0],
              ["Huaman Bravo Barland","Huaman Bravo Barland Alfonso",1],//Editado
              ["Ibañez Bocanegra Adler Jampier","Ibañez Bocanegra Jampier",0],
              ["Ibarra Zapata Ronald Eduardo","Ibarra Zapata Ronal",1],
              ["Jara Estrada Luz Yolanda","Jara Estrada Luz",0],
              ["Lama Isminio Demetrio","Lama Isminio Demetrio Angelo",0],
              ["Lao Olivares Ceila Paquita","Lao Olivares Celia Paquita",0],
              ["Lindo Pizarro Cesar","Lindo Pizarro Cesar Fidel",1],
              ["Lino Duran Tony Michael","Lino Duran Tony Michel",0],
            ["Lopez Villanueva Emel","Lopez Villanueva Antonio Emel",0],
            ["Mayta Molina Carlos","Mayta Molina Carlos Walter",0],
            ["Macavilca Ticalayauri Edwin","Macavilca Ticlayauri Edwin Antonio",0],
            ["Malpartida Marquez Jose Darwin","Malpartida Marquez Darwin",0],
            ["Malpartida Pacheco Jeens R.","Malpartida Pacheco Jeens Ronel",1],
            ["Manrique Ramos Miguel","Manrrique Ramos Miguel Angel",0],
            ["Marin Chavez Cesar Octavio","Marin Chavez Cesar",0],
            ["Marquez Tello Oscar","Marquez Tello Oscar Roberto",0],
            ["Martinez Trujillo Mario Andre","Martinez Trujillo Mario Andres",0],
            ["Menacho Mallqui Tomas","Menacho Mallqui Tomas Aquino",1],
            ["Moreno Grandez Libia Soledad","Moreno Grandez Soledad",0],
            ["Morales Ramirez Roosevelt C.","Morales Ramirez Roosevelt Clemente",0],
            //jose
            ["Mucha Huaman Walter","Mucha Huaman Walter Eduardo",1],//Editado
            ["Muñoz Berrocal Milthon","Muñoz Berrocal Milthon Honorio",1],//Editado
            ["Najar Rivadeneira Victor Fernando","Najar Rivadeneira Victor Fernando",0],
            ["Pardo Huayllas Roberto","Pardo Huayllas Roberto C.","Pardo Huayllas Roberto Carlos",0],//3
            ["Palomino Vera Fritz","Palomino Vera Frits",0],
            ["Perez Olano Miguel","Perez Olano Miguel Angel",1],//Editado
            ["Pinedo Cortez Carlos","Pinedo Cortez Carlos Antonio",0],
            ["Portilla Sandoval Lauriano","Portilla Sandoval Laureano",0],
            ["Puerta Tuesta Ronald","Puerta Tuesta Ronald Hugo",0],
            ["Quispe Janampa David","Quispe Janampa David Prudencio",1],//Editado
            ["Ramirez Trujillo Yolanda","Ramirez Trujillo Yolanda Jesus",1],//Editado
            ["Reategui Diaz Darlym","Reategui Diaz Darlyn",0],
            ["Rios Sifuentes Jonel","Rios Sifuentes Frank Jonel",0],
            ["Rios Ruiz Rolando","Rios Ruiz Rolando Alfredo",1],//Editado
            ["Rivera y Ibarcena Nila","Rivera y Ibarcena Nila Edelmira", 1],//Editado Verificar
            ["Robles Rodriguez Rafael","Robles Rodriguez Rafael Rene", 0],//caso
            ["Robles Rodriguez Monica Trinidad","Robles Rodriguez Monica",0],
            ["Rodriguez Delgado Italo","Rodriguez Delgado Italo Martin",0],
            ["Rodriguez Perez Jessica","Rodriguez Perez Jessica Maria",0],
            ["Ruiz Castre Sandro","Ruiz Castre Sandro Junior",0],
            ["Ruiz Tello Analiz Lola","Ruiz Tello Anaiz Lola",0],
            ["Sanchez Perez Gladys","Sanchez Perez Gladis",0],
            ["Silva Rios Carlos","Silva Rios Carlos Alberto",0],
            ["Soto Calderon Javier","Soto Calderon Javier Maximino",0],
            ["Torres Medina Hildebrando","Torres Medina Hilderbrando",0],
            ["Vargas Clemente Ytavclerh","Vargas Clemente Itavclerh",0],
            ["Vargas Solorzano Jhony","Vargas Solorzano Jhony William",0],
            ["Vasquez Rengifo Jin","Vasquez Rengifo Jin Erick",0],
            ["Vela Zevallos Andy Williams","Vela Zevallos Andy W.",0],
            ["Vergara Julca Americo","Vergara Julca Nonato Americo",0],
            ["Viena Pezo Maria Rosario","Viena Pezo Paredes Maria Rosario",0],
            ["Villanueva Tiburcio Juan","Villanueva Tiburcio Juan Edson",0],
            ["Villacorta Lopez Wagner","Villacorta Lopez Wagner Severo",1],//Editado
            ["Zavala Guerrero Sandra","Zavala Guerrero Sandra Lorena",0],
            //editados
            
          // ["Gonzales Manrique de Lara Tito Felipe","Gonzales Manrique de Lara Tito Felipe",1],//Editado
            // ["Morales y Chocano Luis","Morales Y Chocano Luis",1],
           // ["Villaizan y Huerto Jorge","Villaizan y Huerto Jorge Luis",1], 
        ];

        foreach($list as $item ){
            $p1_ = $item[0];
            $p2_ = $item[1]; 
            $p3_ = count($item)==4?$item[2]:false;
            $i_correct = $item[count($item)-1];
            /* select lastname, name, count(id) from people p 
where lastname != '' and  POSITION(' ' in name) > 0
group by lastname,  SUBSTRING(name, 1, POSITION(' ' in name)-1 )  having count(id)>1; */
            echo "Resolviendo parecidos $p1_\n";
            $p1 = \App\Models\Person::whereRaw( "concat(lastname,' ',name) = '$p1_' " )->first();
            $p2 = \App\Models\Person::whereRaw( "concat(lastname,' ',name) = '$p2_' " )->first();
            if( $p3_ ) $p3 = \App\Models\Person::whereRaw( "concat(lastname,' ',name)='$p3_' " )->first();
            if($i_correct==0){
                $p_correcta= $p1;
                $p_duplicada= $p2; 
                if( $p3_ ) $p_duplicada3= $p3; 
            }else if($i_correct==1){
                $p_correcta= $p2;
                $p_duplicada= $p1; 
                if( $p3_ ) $p_duplicada3= $p3; 
            }
            \App\Models\ResearchAuthor::where( ["author_id"=>$p_duplicada->id] )->update(['author_id'=>$p_correcta->id ]);
            if( $p3_ ) \App\Models\ResearchAuthor::where( ["author_id"=>$p_duplicada3->id] )->update(['author_id'=>$p_correcta->id ]);

            \App\Models\OutcomeAuthor::where( ["author_id"=>$p_duplicada->id] )->update(['author_id'=>$p_correcta->id ]);
            if( $p3_ ) \App\Models\OutcomeAuthor::where( ["author_id"=>$p_duplicada3->id] )->update(['author_id'=>$p_correcta->id ]);

            \App\Models\Person::destroy($p_duplicada->id);
            if( $p3_ ) \App\Models\Person::destroy($p_duplicada3->id);

        }
        \App\Models\Person::where("id",324)->update(['email'=>"tito.gonzalez@unas.edu.pe"]); 
        \App\Models\Person::where("id",141)->update(['email'=>"luis.morales@unas.edu.pe"]); 
        \App\Models\Person::where("id",150)->update(['email'=>"jorge.villaizan@unas.edu.pe"]); 

        \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Albujar Nateros Yoc - Linn'" )->update(['type'=>'D']);
        \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Alva Valdiviezo Wilfredo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Adriazola Del Aguila Jorge Luis' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Alvarez Melo Jorge' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Arevalo Arevalo Carlos Enrique' " )->update(['type'=>'D']);

            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Arevalo Ramirez Carlos Segundo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Ascencio Tadeo Jose Carlos' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) ='Balcazar Terrones Luz Elita' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Bazan Rivera Jimmy Roshimber' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Blas Matienzo Jose Antonio' " )->update(['type'=>'D']);
           \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Bravo Morales Nino Frank' " )->update(['type'=>'D']);
           \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Cabezas Huayllas Oscar Esmael' " )->update(['type'=>'D']);
           \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Caceres Almenara Eduardo Alejandro' " )->update(['type'=>'D']);
           \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Carmona Ruiz Alfredo Abelardo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Castillo Soto Wilson' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Chuqui Paulino Franz Joel' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Coz Rodriguez Edward Javier' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Davila Honorio Duany' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Del Aguila Angulo Marianela Br.' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Del Valle Manyari Luis' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Chavez Asencio Ricardo Martin' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Chia Wong Julio A.' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Coaguila Rodriguez Perci' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Dionisio Garma Maximo Alfredo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Escobar Romero Leonardo Ivan' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Ferrer Tarazona Royer' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Florida Rofner Nelino' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Garcia Caballero Ruth Esther' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Gonzales Huiman Fernando Segundo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Guerra Lu Jose Kalion' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Guerrero Vejarano Tania Elizabeth' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Guerrero Cabrera Jesus' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Guevara Yberico Victor Alfredo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Gutierrez Collao Jairo' " )->update(['type'=>'D']);
             \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Huaman Ortega Melida Karina' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Huaman Bravo Barland Alfonso' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Ibarra Zapata Ronal' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Jara Estrada Luz Yolanda' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Lama Isminio Demetrio' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Lao Olivares Ceila Paquita' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Lindo Pizarro Cesar Fidel' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Lino Duran Tony Michael' " )->update(['type'=>'D']);
              \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Lopez Villanueva Emel' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Mayta Molina Carlos' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Macavilca Ticalayauri Edwin' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Malpartida Marquez Jose Darwin' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Malpartida Pacheco Jeens Ronel' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Manrique Ramos Miguel' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Marin Chavez Cesar Octavio' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Marquez Tello Oscar' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Martinez Trujillo Mario Andre' " )->update(['type'=>'D']);
            
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Menacho Mallqui Tomas Aquino' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Moreno Grandez Libia Soledad' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Morales Ramirez Roosevelt C.' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Mucha Huaman Walter Eduardo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Muñoz Berrocal Milthon Honorio' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Najar Rivadeneira Victor Fernando' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Pardo Huayllas Roberto' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Palomino Vera Fritz' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Perez Olano Miguel Angel' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Pinedo Cortez Carlos' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Portilla Sandoval Lauriano' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Puerta Tuesta Ronald' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Quispe Janampa David Prudencio' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Ramirez Trujillo Yolanda Jesus' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Reategui Diaz Darlym' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Rios Sifuentes Jonel' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Rios Ruiz Rolando Alfredo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Rivera y Ibarcena Nila Edelmira' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Robles Rodriguez Rafael' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Robles Rodriguez Monica Trinidad' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Ruiz Castre Sandro' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Ruiz Tello Analiz Lola' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Sanchez Perez Gladys' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Silva Rios Carlos' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Soto Calderon Javier' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Torres Medina Hildebrando' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Vargas Clemente Ytavclerh' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Vargas Solorzano Jhony' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Vasquez Rengifo Jin' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Vela Zevallos Andy Williams' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Vergara Julca Americo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Viena Pezo Maria Rosario' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Villanueva Tiburcio Juan' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Villacorta Lopez Wagner Severo' " )->update(['type'=>'D']);
            \App\Models\Person::whereRaw( "concat(lastname,' ',name) = 'Zavala Guerrero Sandra' " )->update(['type'=>'D']);
}


}
