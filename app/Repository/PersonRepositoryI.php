<?php
namespace App\Repository;

interface PersonRepositoryI{
   public function all($params);
   public function list_roles($params);
   public function save($params);
   public function get_photo($id);
   public function get_author($id);
   public function get_author_activity($id);
   public function delete($id);
}