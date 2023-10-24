<?php
namespace App\Repository;

interface EventRepositoryI{
   public function all($params);
   public function save($params);
   public function delete($params);
}