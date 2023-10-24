<?php
namespace App\Repository;

interface DocumentRepositoryI{
   public function all($params);
   public function save($params);
   public function delete($params);
}