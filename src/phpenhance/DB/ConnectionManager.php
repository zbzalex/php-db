<?php

namespace phpenhance\DB;

class ConnectionManager
{
  /**
   * @var \phpenhance\DB\Conenction[]
   */
  private $connections = [];

  /**
   * @var string
   */
  private $defaultConnectionName = "default";

  public function __construct($defaultConnectionName = "default")
  {
    $this->defaultConnectionName = $defaultConnectionName;
  }

  /**
   * @return \phpenhance\DB\Connection|null
   */
  public function getConnection($name = null)
  {
    $name = $name === null ? $this->defaultConnectionName : $name;
    return isset($this->connections[$name]) ? $this->connections[$name] : null;
  }

  /**
   * @return void
   */
  public function setConnection($name, Connection $connection)
  {
    $this->connections[$name] = $connection;
  }

  /**
   * @return string
   */
  public function getDefaultConnectionName()
  {
    return $this->defaultConnectionName;
  }

  /**
   * @return void
   */
  public function setDefaultConnectionName($name)
  {
    return $this->defaultConnectionName;
  }
}
