<?php

namespace phpenhance\DB;

class Meta
{
  public $tableName;
  public $columns;

  public function __construct($tableName, array $columns = [])
  {
    $this->tableName = $tableName;
    $this->columns   = $columns;
  }
}