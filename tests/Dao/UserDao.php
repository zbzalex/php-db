<?php

namespace Tests\Dao;

use Tests\Model\User;

/**
 * @Dao
 */
interface UserDao
{
  /**
   * @Query("select * from `users` limit @offset, @limit;")
   * @Emit ("\Tests\Model\User[]")
   */
  function getAll($offset, $limit);

  /**
   * @Query("select * from `users` where `is_bot` = 0 and `last_update` >= (UNIX_TIMESTAMP() - 60 * 15) order by `last_update` desc limit @offset, @limit;")
   * @Emit ("\Tests\Model\User[]")
   */
  function getListOnline($offset, $limit);

  /**
   * @Query("select * from users where id=@id;")
   * @Emit ("\Tests\Model\User")
   */
  function getUser($id);

  /**
   * @Insert
   */
  function insert(User $model);

  /**
   * @Update
   */
  function update(User $model);

  /**
   * @Delete
   */
  function delete($id);
}