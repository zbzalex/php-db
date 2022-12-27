<?php

namespace DB;

class Connection
{
  private $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function getPdo()
  {
    return $this->pdo;
  }

  public function id()
  {
    return $this->pdo->lastInsertId();
  }
  
  public function executeNativeQuery($sql, array $params = [])
  {
    $st = $this->pdo->prepare($sql);
    $st->execute($params);

    return $st;
  }
}