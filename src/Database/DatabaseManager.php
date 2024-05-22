<?php

namespace App\Database;
use Psr\Log\LoggerInterface;

class DatabaseManager implements DatabaseInterface {

  private $connections;
  
  public function __construct(LoggerInterface $logger){
    $this->connections = [
      'mysql' => new MysqlConnection($logger)
    ];
  }

  public function getConnection($name){
    return $this->connections[$name] ?? null;
  }

  public function select(string $select, string $table, string $where = "", string $and = "", string $group = "", string $order = "", string $limit = ""){
    return $this->getConnection('mysql')->select($select, $table, $where, $and, $group, $order, $limit);
  }
  public function update(string $table, array $values, $where, $and = ""){
    return $this->getConnection('mysql')->update($table, $values, $where, $and);
  }
  public function insert(string $table, array $values){
    return $this->getConnection('mysql')->insert($table, $values);
  }
  public function delete(string $table, array $values){
    return $this->getConnection('mysql')->delete($table, $values);
  }
  public function runSelect($sql){
    return $this->getConnection('mysql')->runSelect($sql);
  }
  public function commit(){
    return $this->getConnection('mysql')->commit();
  }

}
