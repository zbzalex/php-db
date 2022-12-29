<?php

namespace Tests\Model;

use phpenhance\DB\Model;

/**
 * @Model("users")
 */
class User extends Model
{
  /**
   * @Id
   * @Column
   */
  public $id;

  /**
   * @Column
   */
  public $login;

  /**
   * @Column
   */
  public $password;

  /**
   * @Column(dataType = "int")
   */
  public $sex;

  /**
   * @Column
   */
  public $ip;

  /**
   * @Column
   */
  public $soft;

  /**
   * @Column(dataType = "int")
   */
  public $level;

  /**
   * @Column(dataType = "int")
   */
  public $exp;

  /**
   * @Column(dataType = "int", name = "credits")
   */
  public $money;

  /**
   * @Column(dataType = "int")
   */
  public $x;

  /**
   * @Column(dataType = "int")
   */
  public $y;

  /**
   * @Column(dataType = "int")
   */
  public $online;

  /**
   * @Column(name = "access_level", dataType = "int")
   */
  public $accessLevel;

  /**
   * @Column
   */
  public $email;

  // deprecated
  //public $pcid;

  /**
   * @Column(name = "is_bot", dataType = "int")
   */
  public $isBot;
  
  public $kind;

  /**
   * @Column
   */
  public $block;

  /**
   * @Column(name = "force", dataType = "int")
   */
  public $strength;

  /**
   * @Column(name = "dexterity", dataType = "int")
   */
  public $dex;

  /**
   * @Column(name = "int", dataType = "int")
   */
  public $luck;

  /**
   * @Column(dataType = "int")
   */
  public $health;

  /**
   * @Column(dataType = "int")
   */
  public $intellect;

  /**
   * @Column(name = "add_strength", dataType = "int")
   */
  public $addStrength;

  /**
   * @Column(name = "add_dex", dataType = "int")
   */
  public $addDex;

  /**
   * @Column(name = "add_luck", dataType = "int")
   */
  public $addLuck;

  /**
   * @Column(name = "add_intellect", dataType = "int")
   */
  public $addIntellect;

  /**
   * @Column(name = "curhp", dataType = "int")
   */
  public $curHp;

  /**
   * @Column(name = "maxhp", dataType = "int")
   */
  public $maxHp;

  /**
   * @Column(name = "hp_per_second", dataType = "int")
   */
  public $hpPerSecond;

  /**
   * @Column(name = "curmp", dataType = "int")
   */
  public $curMp;

  /**
   * @Column(name = "maxmp", dataType = "int")
   */
  public $maxMp;

  /**
   * @Column(name = "mp_per_second", dataType = "int")
   */
  public $mpPerSecond;

  /**
   * @Column
   */
  public $nick;

  /**
   * @Column(dataType = "int")
   */
  public $battle;

  /**
   * @Column(name = "side", dataType = "int")
   */
  public $team;

  // deprecated
  //public $st;

  /**
   * @Column(name = "___hp", dataType = "int")
   */
  public $hpUpdatedAt;

  /**
   * @Column(name = "___mp", dataType = "int")
   */
  public $mpUpdatedAt;

  // deprecated
  //public $skills;

  /**
   * @Column(name = "curmass", dataType = "int")
   */
  public $curMass;

  /**
   * @Column(name = "maxmass", dataType = "int")
   */
  public $maxMass;

  /**
   * @Column(name = "_dmg", dataType = "int")
   */
  public $damage;

  /**
   * @Column(dataType = "int")
   */
  public $align;

  /**
   * @Column(name = "obraz", dataType = "int")
   */
  public $pic;

  /**
   * @Column(name = "kill", dataType = "int")
   */
  public $kills;

  /**
   * @Column(
   * name = "last_update",
   * dataType  = "int"
   * )
   */
  public $updatedAt;

  // deprecated
  //public $stats;

  /**
   * @Column
   */
  public $about;

  /**
   * @Column
   */
  public $country;

  /**
   * @Column(name = "leveluppoints", dataType = "int")
   */
  public $levelUpPoints;

  // deprecated
  //public $status;

  /**
   * @Column(name = "skillpoints", dataType = "int")
   */
  public $skillPoints;

  /**
   * @Column
   */
  public $name;

  /**
   * @Column(name = "flash_password")
   */
  public $flashPassword;

  /**
   * @Column(
   * name = "saved",
   * dataType  = "int"
   * )
   */
  public $createdAt;

  // deprecated
  //public $loc;

  /**
   * @Column(name = "sleep", dataType = "int")
   */
  public $wait;
  
  public $workType;

  /**
   * @Column(name = "px", dataType = "int")
   */
  public $x2;

  /**
   * @Column(name = "py", dataType = "int")
   */
  public $y2;

  // deprecated
  //public $eurocredits;
  // deprecated
  //public $dealer;
  // deprecated
  //public $lastDailyPresentTake;

  /**
   * @Column(dataType = "int")
   */
  public $invis;
  // deprecated
  //public $premium;

  // deprecated
  /**
   * @Column(dataType = "int")
   */
  public $totem;
  // deprecated
  //public $mute;

  /**
   * @Column(name = "block_shield", dataType = "int")
   */
  public $blockShield;

  /**
   * @Column(dataType = "int")
   */
  public $skill1;

  /**
   * @Column(dataType = "int")
   */
  public $skill2;

  /**
   * @Column(dataType = "int")
   */
  public $skill3;

  /**
   * @Column(dataType = "int")
   */
  public $skill4;

  /**
   * @Column(dataType = "int")
   */
  public $skill5;

  /**
   * @Column(dataType = "int")
   */
  public $skill6;

  /**
   * @Column(dataType = "int")
   */
  public $skill7;

  /**
   * @Column(dataType = "int")
   */
  public $skill8;

  /**
   * @Column(dataType = "int")
   */
  public $skill9;

  /**
   * @Column(dataType = "int")
   */
  public $skill10;

  /**
   * @Column(dataType = "int")
   */
  public $skill11;

  /**
   * @Column(dataType = "int")
   */
  public $skill12;

  /**
   * @Column(dataType = "int")
   */
  public $skill13;

  /**
   * @Column(dataType = "int")
   */
  public $skill14;

  /**
   * @Column(dataType = "int")
   */
  public $skill15;

  /**
   * @Column(dataType = "int")
   */
  public $skill16;

  /**
   * @Column(dataType = "int")
   */
  public $skill17;

  /**
   * @Column(dataType = "int")
   */
  public $skill18;

  /**
   * @Column(dataType = "int")
   */
  public $skill19;

  /**
   * @Column(dataType = "int")
   */
  public $skill20;

  /**
   * @Column(name = "min_damage", dataType = "int")
   */
  public $minDamage;

  /**
   * @Column(name = "max_damage", dataType = "int")
   */
  public $maxDamage;

  /**
   * @Column(dataType = "int")
   */
  public $evasion;

  /**
   * @Column(dataType = "int")
   */
  public $accuracy;

  /**
   * @Column(dataType = "int")
   */
  public $crit;

  /**
   * @Column(dataType = "int")
   */
  public $durability;

  /**
   * @Column(name = "armorclass", dataType = "int")
   */
  public $armorClass;

  /**
   * @Column(name = "armorbreak", dataType = "int")
   */
  public $armorBreak;

  /**
   * @Column(dataType = "int")
   */
  public $drafts;

  /**
   * @Column(dataType = "int")
   */
  public $victories;

  /**
   * @Column(dataType = "int")
   */
  public $defeats;

  /**
   * @Column
   */
  public $city;

  /**
   * @Column
   */
  public $house;

  /**
   * @Column
   */
  public $room;

  /**
   * @Column
   */
  public $birth;

  /**
   * @Column(name = "add_skill1", dataType = "int")
   */
  public $addSkill1;

  /**
   * @Column(name = "add_skill2", dataType = "int")
   */
  public $addSkill2;

  /**
   * @Column(name = "add_skill3", dataType = "int")
   */
  public $addSkill3;

  /**
   * @Column(name = "add_skill4", dataType = "int")
   */
  public $addSkill4;

  /**
   * @Column(name = "add_skill5", dataType = "int")
   */
  public $addSkill5;

  /**
   * @Column(name = "add_skill6", dataType = "int")
   */
  public $addSkill6;

  /**
   * @Column(name = "add_skill7", dataType = "int")
   */
  public $addSkill7;

  /**
   * @Column(name = "add_skill8", dataType = "int")
   */
  public $addSkill8;

  /**
   * @Column(name = "add_skill9", dataType = "int")
   */
  public $addSkill9;

  /**
   * @Column(name = "add_skill10", dataType = "int")
   */
  public $addSkill10;

  /**
   * @Column(name = "add_skill11", dataType = "int")
   */
  public $addSkill11;

  /**
   * @Column(name = "add_skill12", dataType = "int")
   */
  public $addSkill12;

  /**
   * @Column(name = "add_skill13", dataType = "int")
   */
  public $addSkill13;

  /**
   * @Column(name = "add_skill14", dataType = "int")
   */
  public $addSkill14;

  /**
   * @Column(name = "add_skill15", dataType = "int")
   */
  public $addSkill15;

  /**
   * @Column(name = "add_skill16", dataType = "int")
   */
  public $addSkill16;

  /**
   * @Column(name = "add_skill17", dataType = "int")
   */
  public $addSkill17;

  /**
   * @Column(name = "add_skill18", dataType = "int")
   */
  public $addSkill18;

  /**
   * @Column(name = "add_skill19", dataType = "int")
   */
  public $addSkill19;

  /**
   * @Column(name = "add_skill20", dataType = "int")
   */
  public $addSkill20;
}
