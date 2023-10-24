<?php
namespace App\Repository;

interface CategoryRepositoryI{
   public function all($params);
   public function list_programs_and_lines($params);
   public function save($params);
   public function get_members($id);
}