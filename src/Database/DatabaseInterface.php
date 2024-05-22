<?php

namespace App\Database;

interface DatabaseInterface {

  public function select(string $select, string $table, string $where = "", string $and = "", string $group = "", string $order = "", string $limit = "");
  public function update(string $table, array $values, $where, $and = "");
  public function insert(string $table, array $values);
  public function delete(string $table, array $values);
  public function runSelect($sql);

  public function commit();
}
