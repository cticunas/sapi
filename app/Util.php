<?php
namespace App;
use Illuminate\Support\Facades\Storage;

class Util{


    public static $RT_AUXIL=5;
    public static $RT_COBRA=6;
    public static $CA_CASHB=7;

    public static $ROL_PERSON_ID=3;
    public static $COMPANY_ID=1;
    public static $debts_wait_for_notify=1;

    public static function check_mandatory_fields($data, $mandatory_params){
        foreach($mandatory_params as $v){ 
            if(  !array_key_exists($v,$data) ) throw new \Exception("$v is mandatory"); 
        }
        return true;
    }
    public static function namePhoto($name){   
        if(Storage::disk('local')->exists("public/".$name.".jpg")){
            return $name."x";
        }else if(Storage::disk('local')->exists("public/".$name."x".".jpg")){
            return $name;
        }else{
            return $name;
        }
    }
    public static function storageImage($name, $base64_content){
        $base64_str = substr($base64_content, strpos($base64_content, ",")+1);
        $image = base64_decode($base64_str);
       //se hace esto cambio de nombre porque en la vista no renderiza con el mismo nombre ya que vue no detecta cambio
        if(Storage::disk('local')->exists("public/".$name.".jpg")){//verifica si existe el archivo
            Storage::delete("public/".$name.".jpg");//elimina la foto existente
            Storage::put("public/".$name.".jpg",$image);
            return Storage::url("./public/".$name.".jpg");
        }else if(Storage::disk('local')->exists("public/".$name.".jpg")){
            Storage::delete("public/".$name.".jpg");//elimina la foto existente
            Storage::put("public/".$name.".jpg",$image);
            return Storage::url("./public/".$name.".jpg");
        }else{
            Storage::put("public/".$name.".jpg",$image);
            return Storage::url("./public/".$name.".jpg");
        }
    }
    public static function storageFile($name, $file){
        Storage::put("public/files/".$name,$file);
        return Storage::url("./public/files/".$name);
    }

    public static function verifyFile($params){
        if(array_key_exists("file",$params)&& !empty($params['file'])){
            $file = $params['file'];
        }else{
            $file="";
        }
        return $file;
    }

    public static function verifyPhoto($params){
        if(array_key_exists("photo",$params)&& ! empty($params['photo'])){
            $photo = $params['photo'];
        }else{
            $photo="";
        }
        return $photo;
    }

    public static function is_base64image($image_content){
        return !empty($image_content) && preg_match('/data:image\/[a-zA-Z]*;base64,/',$image_content);
    }

    public static function separate_files_into($data) {
        $files=null;
        if(array_key_exists("files",$data)){
            $files=$data['files'];
            unset($data['files']);
        }
        return $files;
    }
}
