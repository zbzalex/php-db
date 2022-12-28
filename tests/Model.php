<?php

namespace Tests;

/**
 * @Model("users")
 */
class Model
{
  /**
   * @Id
   * @Column
   */
  public $id;

  /**
   * @Column
   * @Nullable
   */
  public $nick;

  /**
   * @Column(dataType = "int")
   */
  public $sex;

  /**
   * @Column(name = "last_update", dataType = "int")
   */
  public $updatedAt;


  /**
   * @Column(name = "curhp", dataType = "int", default = "5")
   */
  public $curHp;

  /**
   * @Column(name = "maxhp", dataType = "int", default = "7")
   */
  public $maxHp;

  /**
   * @Column(dataType = "text")
   */
  public $about;
}