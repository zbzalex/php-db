<?php

namespace Util;

class IteratorImpl implements IIterator
{
  protected $elements = [];
  protected $cursor = -1;
  public function __construct(array $elements)
  {
    $this->elements = $elements;
  }

  public function hasNext() {
    return $this->cursor < count($this->elements) - 1;
  }

  public function next()
  {
    return $this->elements[++$this->cursor];
  }
}