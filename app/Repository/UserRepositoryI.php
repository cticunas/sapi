<?php
namespace App\Repository;

interface UserRepositoryI{
   public function all($params);
   // public function all_roles($params);
   public function get($id);
   public function save($params);
   public function loginByGoogle($params);
   public function loginByOffice($params);
   public function delete($id);
}