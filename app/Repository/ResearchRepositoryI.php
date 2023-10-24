<?php
namespace App\Repository;

interface ResearchRepositoryI{
   public function all($params);
   public function save($params);
   public function saveStatus($params);
   public function listStatus($params);
   public function delete($params);
   public function public_list($params);
   public function py_by_author($params);
   //public function byAuthorWord($params);
   //public function byStateWord($params);
   public function sunedu_list($params);
   public function constancy_by_author($params);
   public function certified_by_author ($params);
   public function get_author($params);
   public function py_by($params);
   public function by_period($params);
}