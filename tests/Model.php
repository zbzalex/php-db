<?php

namespace Tests;

use phpenhance\Util\TypedArray;

class Model
{
  public $id;
  public static function fromArray(array $data) {
    $data = new TypedArray($data);
    $obj_ = new Model();
    $obj_->id = $data->getInt("id");
    return $obj_;
  }
}