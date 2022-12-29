<?php

namespace Tests;

use phpenhance\DB\Connection;
use phpenhance\DB\DaoMapper;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
  public function test1()
  {
    $connectionManager = new \phpenhance\DB\ConnectionManager();
    $connectionManager->setConnection($connectionManager->getDefaultConnectionName(), new Connection(new \PDO("mysql:host=localhost;dbname=bloodlands", "root", "123")));
    $daoMapper = new DaoMapper($connectionManager->getConnection(), \Tests\Dao\UserDao::class);
    
    //$obj = new \Tests\Model\User();

    $obj = $daoMapper->getUser(50);
    //var_dump($result);
    $obj->set("updatedAt", time());

    var_dump($obj->modified);
    $daoMapper->update($obj);
    var_dump($obj->modified);

    $obj = $daoMapper->getUser(50);
    var_dump($obj->updatedAt);
  }
}
