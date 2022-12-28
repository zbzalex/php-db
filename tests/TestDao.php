<?php

namespace Tests;

interface TestDao
{
  /**
   * @Query("select * from users limit @offset, @limit;")
   * @Emit ("\Tests\Model[]")
   */
  function getAll($offset, $limit);

  /**
   * @Query("select * from users where id=@id;")
   * @Emit ("\Tests\Model")
   */
  function get($id);

  /**
   * @Insert
   */
  function insert($obj);

  /**
   * @Update
   */
  function update($obj);

  /**
   * @Delete
   */
  function delete($id);
}