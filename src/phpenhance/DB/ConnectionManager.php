<?php

namespace phpenhance\DB;

class ConnectionManager
{
  private $connections = [];
  private $defaultConnectionName = "default";

  public function __construct($defaultConnectionName = "default")
  {
    $this->defaultConnectionName = $defaultConnectionName;
  }

  public function getConnection($name = null)
  {
    $name = $name === null ? $this->defaultConnectionName : $name;
    return isset($this->connections[$name]) ? $this->connections[$name] : null;
  }

  public function setConnection($name, Connection $connection)
  {
    $this->connections[$name] = $connection;
  }

  public function getDefaultConnectionName()
  {
    return $this->defaultConnectionName;
  }

  public function setDefaultConnectionName($name)
  {
    return $this->defaultConnectionName;
  }
}
