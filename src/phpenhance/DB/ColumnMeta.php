<?php

namespace phpenhance\DB;

class ColumnMeta
{
  public $name;
  public $dataType;
  public $length;
  public $autoIncrement = false;
  public $default;

  public function __construct($name, $dataType, $length, $authoIncrement = false, $default = null)
  {
    $this->name = $name;
    $this->dataType = $dataType;
    $this->length = $length;
    $this->autoIncrement = $authoIncrement;
    $this->default = $default;
  }
}
