<?php

namespace DB;

class EmitValue
{
  protected $class;
  public function __construct($class)
  {
    $this->class = $class;
  }

  public function getClass()
  {
    return $this->class;
  }
}
