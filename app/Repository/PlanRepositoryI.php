<?php
namespace App\Repository;

use App\Model\User;
use Illuminate\Support\Collection;

interface PlanRepositoryI{
   public function all($params);
   public function save($params);
   public function list_lines($params);
   public function save_line_actives($params);
   public function delete($params);
}