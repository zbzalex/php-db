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
    $daoMapper = new DaoMapper($connectionManager->getConnection(), \Tests\TestDao::class);
    
    $obj = new \Tests\Model();
    $obj->nick = "hello";
    $obj->sex  = 1;
    // $obj->curHp = 5;
    // $obj->maxHp = 7;
    $obj->about = "asd";

    $obj = $daoMapper->insert($obj);

    // var_dump($obj);

    // $obj->curHp = null;

    // $obj = $daoMapper->update($obj);

    // var_dump($obj);

    $obj = $daoMapper->get($obj->id);

    var_dump($obj);

    $daoMapper->delete($obj);
  }
}
