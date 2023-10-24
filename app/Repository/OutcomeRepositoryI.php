<?php
namespace App\Repository;

interface OutcomeRepositoryI{
   public function all($params);
   public function save_outcome($params);
   public function save_pending_outcomes($params);
   public function delete($params);
   public function investigations_by_trimester($params);
   public function out_by($params);
  // public function byOutWord($params);
}
