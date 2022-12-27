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
   * @Emit ("\Tests\Model")
   */
  function getAll($offset, $limit);
}
```

```
<?php

namespace Tests;

use Util\TypedArray;

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
```

```
<?php

...

$connectionManager = new \DB\ConnectionManager();
$connectionManager->setConnection($connectionManager->getDefaultConnectionName(), new Connection(new \PDO("mysql:host=localhost;dbname=test", "root", "")));
$daoMapper = new DaoMapper($connectionManager->getConnection(), \Tests\TestDao::class);
$result = $daoMapper->getAll(0, 10);

var_dump($result);

...
```
