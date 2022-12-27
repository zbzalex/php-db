<?php

namespace Util;

class TypedArray
{
  private $data;
  
  public function __construct(array $data)
  {
    $this->data = $data;
  }

  public function getInt($key, $default = 0)
  {
    return isset($this->data[$key]) ? (int) $this->data[$key] : $default;
  }

  public function getFloat($key, $default = 0.0)
  {
    return isset($this->data[$key]) ? (float) $this->data[$key] : $default;
  }

  public function getDouble($key, $default = 0.0)
  {
    return isset($this->data[$key]) ? (float) $this->data[$key] : $default;
  }

  public function getString($key, $default = null)
  {
    return isset($this->data[$key]) ? $this->data[$key] : $default;
  }

  public function getBool($key, $default = false)
  {
    return isset($this->data[$key]) ? (bool) $this->data[$key] : $default;
  }

  public function getTypedArray($key)
  {
    return isset($this->data[$key]) && is_array($this->data[$key]) ? new TypedArray($this->data[$key]) : new TypedArray([]);
  }
}
