<?php
namespace App\Repository;

use App\Model\User;
use Illuminate\Support\Collection;

interface JournalRepositoryI{
   public function all($params);
   public function save($params);
   public function delete($params);
}