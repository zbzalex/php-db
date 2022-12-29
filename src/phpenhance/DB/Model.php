<?php

namespace phpenhance\DB;

abstract class Model
{
  public $modified;
  public function __construct(array $modified = []) {
    $this->modified = $modified;
  }

  public function set($property, $value)
  {
    $reflector = new \ReflectionClass($this);
    $propertyReflector = $reflector->getProperty($property);
    $propertyReflector->setValue($this, $value);

    $this->modified[] = $property;
  }
}