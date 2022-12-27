<?php

namespace phpenhance\DB;

class EmitValue
{
  /**
   * @var string
   */
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
