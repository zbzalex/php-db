<?php

namespace Tests;

interface TestDao
{
  /**
   * @Query("select * from users limit @offset, @limit;")
   * @Emit ("\Tests\Model")
   */
  function getAll($offset, $limit);
}