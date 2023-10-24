<?php
namespace App\Repository;

interface FileRepositoryI{
   public function all($params);
   public function save($params);
   public function check_and_save($files,$object);
   public function delete($params);
}