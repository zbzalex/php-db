php db
===============

php db

```
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
   * @Query("select * from users limit 1;")
   * @Emit ("\Tests\Model")
   */
  function get($id);

  /**
   * @Insert
   */
  function insert($obj);

  /**
   * @Insert
   */
  function update($obj);

  /**
   * @Delete
   */
  function delete($obj);
}
```

```
<?php

namespace Tests;

use phpenhance\Util\TypedArray;

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
   */
  public $nick;

  /**
   * @Column(dataType = "int")
   */
  public $sex;
  
  /**
   * @Column(name = "created_at", dataType = "int")
   */
  public $createdAt;
}
```

```
<?php

// ...

$connectionManager = new \phpenhance\DB\ConnectionManager();
$connectionManager->setConnection($connectionManager->getDefaultConnectionName(), new Connection(new \PDO("mysql:host=localhost;dbname=test", "root", "")));
$daoMapper = new DaoMapper($connectionManager->getConnection(), \Tests\TestDao::class);
$result = $daoMapper->getAll(0, 10);

var_dump($result);

// ...
```
