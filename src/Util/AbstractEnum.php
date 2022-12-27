<?php

namespace Util;

abstract class AbstractEnum
{
  protected static $cachedConstants = [];

  protected static function getConstants()
  {
    $calledClass = get_called_class();
    if (!array_key_exists($calledClass, static::$cachedConstants)) {
      $reflection  = new \ReflectionClass($calledClass);
      $cachedConstants[$calledClass] = $reflection->getConstants();
    }

    return $cachedConstants[$calledClass];
  }

  public static function isValid($name)
  {
    $constants = static::getConstants();

    return in_array($name, array_keys($constants));
  }

  public static function isValidValue($value)
  {
    $constants = static::getConstants();

    return in_array($value, array_values($constants));
  }
}
