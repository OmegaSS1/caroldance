<?php

namespace App\Database;

use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

class Mysqlconnection implements DatabaseInterface {

    private $connection;
    private string $loggerMessage = "Erro inesperado na validação dos dados! Consulte o administrador do sistema.";
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger = $logger;

        $conn = new PDO("mysql:host=".ENV['HOST'].";dbname=".ENV['DBNAME'].";charset=".ENV['CHARSET'], ENV['USER'], ENV['PASSWORD']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $this->connection = $conn;
        $this->connection->beginTransaction();
    }

  /**
     * RETORNA A ESTRUTURA DO BANCO
     * @var string
     * @return array
     */
    public function getColumnMeta(string $table)
    {
        try {
            ## Pega o tamanho maximo permitido de cada coluna
            $l = "SELECT character_maximum_length FROM information_schema.columns WHERE table_name = '$table'";
            $stm = $this->connection->prepare($l);
            $stm->execute();
            $l = $stm->fetchAll();

            ## Pega a estrutura de cada coluna
            $m = "SELECT * FROM $table";
            $stm = $this->connection->prepare($m);
            $stm->execute();

            $countColumn = $stm->columnCount();
            for ($i = 0; $i < $countColumn; $i++) {
                $meta[] = $stm->getColumnMeta($i);
                $meta[$i]['length'] = @$l[$i]['character_maximum_length'];
            };
            return $meta;
        } catch (PDOException $e) {
            $this->logger->info("[Database (GETCOLUMNMETA) - IP ".IP."]", ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new PDOException($this->loggerMessage);
        }
    }

    /**
     * RETORNA A CONSULTA NO BANCO (PARA QUERIES MAIS COMPLEXAS E EXTENSAS)
     * @var string
     * @return array
     */
    public function runSelect($sql)
    {
        try {

            $stm = $this->connection->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->error("[Database (RUNSELECT) - IP ".IP."]", ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new PDOException($this->loggerMessage);
        }
    }

    /**
     * Executa qualquer query
     * @var string
     * @return int
     */
    public  function run($sql)
    {
      try {

          $stm = $this->connection->prepare($sql);
          $stm->execute();
          return $this->connection->lastInsertId();
      } catch (PDOException $e) {
        $this->logger->error("[Database (RUN) - IP ".IP."]", ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()]);
        throw new PDOException($this->loggerMessage);
      }
    }

    /**
     * @RETORNA O TOTAL DA CONSULTA NO BANCO (PARA QUERIES MAIS COMPLEXAS E EXTENSAS)
     * @var string
     * @return int
     */
    public  function runRow($sql)
    {
      try {

          $stm = $this->connection->prepare($sql);
          $stm->execute();
          return $stm->rowCount();
      } catch (PDOException $e) {
        $this->logger->error("[Database (RUNROW) - IP ".IP."]", ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()]);
        throw new PDOException($this->loggerMessage);
      }
    }


    public function row($select, $table, $where = "", $and = "", $order = "", $limit = "")
    {
      $where = strlen($where) ? 'WHERE '   . $where : '';
      $and   = strlen($and)   ? 'AND '     . $and   : '';
      $order = strlen($order) ? 'ORDER BY ' . $order : '';
      $limit = strlen($limit) ? 'LIMIT '   . $limit : '';

      $query = "SELECT $select FROM $table $where $and $order $limit";

      try {

          $stm = $this->connection->prepare($query);
          $stm->execute();
          return $stm->rowCount();
      } catch (PDOException $e) {
        $this->logger->error("[Database (ROW) - IP ".IP."]", ["Code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()]);
        throw new PDOException($this->loggerMessage);
      }
    }

    public function select(string $select, string $table, string $where = "", string $and = "", string $group = "", string $order = "", string $limit = "")
    {
        
        $where = strlen($where) ? 'WHERE '   . $where : '';
        $and   = strlen($and)   ? 'AND '     . $and   : '';
        $group = strlen($group) ? 'GROUP BY ' . $group : '';
        $order = strlen($order) ? 'ORDER BY ' . $order : '';
        $limit = strlen($limit) ? 'LIMIT '   . $limit : '';
        $query = "SELECT $select FROM $table $where $and $group $order $limit";

        try {

            $stm = $this->connection->prepare($query);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->error("[Database (SELECT) - IP ".IP."]", ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new PDOException($this->loggerMessage);
        }
    }

    public function update(string $table, array $values, $where, $and = "")
    {
        
        $and    = strlen($and) ? ' AND ' . $and : '';
        $fields = array_keys($values);

        $query  = "UPDATE $table SET " . implode(" = ?, ", $fields) . " = ? WHERE $where $and";

        self::bindValue($query, $values);
        return true;
    }

    public function insert(string $table, array $values)
    {
        
        $fields = array_keys($values);
        $binds  = array_pad([], count($fields), '?');
        $query  = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES(" . implode(',', $binds) . ")";

        return self::bindValue($query, $values);
 
    }

    public function delete(string $table, array $values)
    {
        
        $fields = array_keys($values);
        $query = "DELETE FROM $table WHERE " . implode(" = ? AND ", $fields) . " = ?";

        return self::bindValue($query, $values);
    }

    private function bindValue($query, array $values){
        try{
            // self::$transaction[] = $this->connection;
            $stm = $this->connection->prepare($query);
            
            $stm->execute(array_values($values));
            $id = $this->connection->lastInsertId();
            return $id;
        }
        catch(PDOException $e){
            $this->connection->rollBack();
            $this->logger->error("[Database (BINDVALUE) - IP ".IP."]", ["code" => $e->getCode(), "message" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new PDOException($this->loggerMessage);
        }
    }

    public function commit(){
      $this->connection->commit();
      $this->connection->beginTransaction();
    }
}
