<?php

namespace phpenhance\DB;

class Connection
{
  /**
   * @var \PDO
   */
  private $pdo;

  /**
   * @var \phpenhance\DB\DaoMapper
   */
  private $daoMapping = null;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * @return \PDO
   */
  public function getPdo()
  {
    return $this->pdo;
  }

  /**
   * @return int
   */
  public function id()
  {
    return $this->pdo->lastInsertId();
  }

  /**
   * @return \PDOStatement
   */
  public function executeNativeQuery($sql, array $params = [])
  {
    $st = $this->pdo->prepare($sql);
    $st->execute($params);

    return $st;
  }

  /**
   * @return object
   */
  public function getDao($daoClass)
  {
    if (!isset($this->daoMapping[$daoClass])) {
      $this->daoMapping[$daoClass] = new DaoMapper($this, $daoClass);
    }

    return $this->daoMapping[$daoClass];
  }
}
