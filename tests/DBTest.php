<?php

namespace Tests;

use DB\Connection;
use DB\DaoMapper;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
  public function test1()
  {
    $connectionManager = new \DB\ConnectionManager();
    $connectionManager->setConnection($connectionManager->getDefaultConnectionName(), new Connection(new \PDO("mysql:host=localhost;dbname=test", "root", "")));
    $daoMapper = new DaoMapper($connectionManager->getConnection(), \Tests\TestDao::class);
    $result = $daoMapper->getAll(0, 10);

    var_dump($result);
  }
}