<?php
namespace App\Repository;

interface AuthorRepositoryI{
   public function all($params);
   public function save($params);
   public function delete($params);
}