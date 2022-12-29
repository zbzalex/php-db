<?php

namespace phpenhance\DB;

use phpenhance\DB\ColumnMeta;
use phpenhance\Annotations\AnnotationReader;
use phpenhance\Util\IteratorImpl;

class DaoMapper
{
  /**
   * @var \phpenhance\DB\Connection
   */
  private $connection;

  /**
   * @var string
   */
  private $daoClass;

  public function __construct(Connection $connection, $daoClass)
  {
    $this->connection = $connection;
    $this->daoClass   = $daoClass;
  }

  public function __call($method, $arguments)
  {
    $reflectionClass = new \ReflectionClass($this->daoClass);
    $reflectionMethod = $reflectionClass->getMethod($method);
    $parameters = $reflectionMethod->getParameters();

    $reader = new AnnotationReader($this->daoClass, $method);
    $reader->parse();

    $insertAnnotation = $reader->getAnnotation("Insert");
    $updateAnnotation = $reader->getAnnotation("Update");
    $deleteAnnotation = $reader->getAnnotation("Delete");

    $queryAnnotation = $reader->getAnnotation("Query");
    if ($queryAnnotation !== null) {
      $queryAnnotationValue = $queryAnnotation->getParameter("value");

      $emitAnnotation = $reader->getAnnotation("Emit");
      if ($emitAnnotation === null) {
        throw new \Exception("@Emit  annotation not found");
      }

      $emitAnnotationValue = $emitAnnotation->getParameter("value");
      $emitValue = $this->getEmitValue($emitAnnotationValue);

      if (count($parameters) != count($arguments)) {
        throw new \Exception("Invalid number of parameters");
      }

      $i = 0;
      $count = count($parameters);
      do {
        $param = $parameters[$i];
        $params[$param->getName()] = $arguments[$i];
      } while ($i++ < $count - 1);


      $compiledQuery = $this->compileQuery($queryAnnotationValue, $params);
      $st = $this->connection->executeNativeQuery($compiledQuery);

      $emitValueClassReflector = new \ReflectionClass($emitValue->getClass());

      $obj = $emitValueClassReflector->newInstance();
      $meta = $this->getMeta($obj);

      if ($emitValue instanceof EmitValueArray) {

        $list = $st->fetchAll(\PDO::FETCH_ASSOC);

        $models = [];

        $it = new IteratorImpl($list);
        while ($it->hasNext()) {
          $arr = $it->next();

          $obj = $emitValueClassReflector->newInstance();

          $reflector = new \ReflectionClass(get_class($obj));
          $properties = $reflector->getProperties();
          $it_ = new IteratorImpl($properties);
          while ($it_->hasNext()) {
            $property = $it_->next();

            if (isset($meta->columns[$property->getName()]) === false) {
              continue;
            }

            $column = $meta->columns[$property->getName()];
            $reflector = new \ReflectionProperty($obj, $property->getName());
            $value     = isset($arr[$column->name]) ? $arr[$column->name] : null;
            $value     = $value !== null
              ? ($column->dataType == "int" ? intval($value) : $value)
              : ($column->dataType == "int" ? ($column->default === null ? 0 : intval($column->default)) : $column->default);

            $reflector->setValue($obj, $value);
          }

          $models[] = $obj;
        }

        return $models;
      } else {

        $arr = $st->fetch(\PDO::FETCH_ASSOC);

        $reflector = new \ReflectionClass(get_class($obj));
        $properties = $reflector->getProperties();
        $it = new IteratorImpl($properties);
        while ($it->hasNext()) {
          $property = $it->next();

          if (isset($meta->columns[$property->getName()]) === false) {
            continue;
          }

          $column = $meta->columns[$property->getName()];
          $reflector = new \ReflectionProperty($obj, $property->getName());
          $value     = isset($arr[$column->name]) ? $arr[$column->name] : null;
          $value     = $value !== null
            ? ($column->dataType == "int" ? intval($value) : $value)
            : ($column->dataType == "int" ? ($column->default === null ? 0 : intval($column->default)) : $column->default);

          $reflector->setValue($obj, $value);
        }

        return $obj;
      }
    } else if ($insertAnnotation !== null) {
      return $this->doInsert($arguments[0]);
    } else if ($updateAnnotation !== null) {
      return $this->doUpdate($arguments[0]);
    } else if ($deleteAnnotation !== null) {
      return $this->doDelete($arguments[0]);
    }
  }

  private function getMeta($obj)
  {
    $class = get_class($obj);

    $annotationReader = new AnnotationReader($class);
    $annotationReader->parse();

    $modelAnnotation = $annotationReader->getAnnotation("Model");
    if ($modelAnnotation === null) {
      throw new \Exception();
    }

    $tableName = $modelAnnotation->getParameter("value");
    if ($tableName === null) {
      throw new \Exception();
    }

    $reflector = new \ReflectionClass($class);
    $properties = $reflector->getProperties();

    $columns = [];

    $it = new IteratorImpl($properties);
    while ($it->hasNext()) {
      $property = $it->next();

      $annotationReader = new AnnotationReader($class, $property->getName(), "property");
      $annotationReader->parse();

      /** @var \phpenhance\Annotations\Annotation $columnAnnotation */
      $columnAnnotation = $annotationReader->getAnnotation("Column");
      if ($columnAnnotation === null) continue;

      $autoIncrement = $annotationReader->getAnnotation("Id") !== null;
      $name       = $columnAnnotation->getParameter("name");
      $dataType   = $columnAnnotation->getParameter("dataType");
      $dataType   = $autoIncrement ? "int" : ($dataType !== null ? strtolower($dataType) : "varchar");
      $length     = $columnAnnotation->getParameter("length");
      $length     = $length !== null ? $length : ($dataType === "int" ? 11 : ($dataType === "text" ? 0 : 45));
      $default    = $columnAnnotation->getParameter("default");

      $columnMeta = new ColumnMeta(
        $name !== null ? $name : $property->getName(),
        $dataType,
        $length,
        $autoIncrement,
        $default !== null ? $default : ($dataType === "int" ? 0 : null)
      );

      $columns[$property->getName()] = $columnMeta;
    }

    return new Meta($tableName, $columns);
  }

  private function doInsert($obj)
  {
    $meta = $this->getMeta($obj);

    $sql = [];
    $sql[] = "insert into `" . $meta->tableName . "`";
    $sql[] = "(";
    $columns = [];
    $it = new IteratorImpl(array_values($meta->columns));
    while ($it->hasNext()) {
      $column = $it->next();
      if ($column->autoIncrement) continue;
      $columns[] = sprintf("`%s`", $column->name);
    }
    $sql[] = implode(",", $columns);
    $sql[] = ") values (";
    $columns = [];
    $it = new IteratorImpl(array_values($meta->columns));
    while ($it->hasNext()) {
      $column = $it->next();
      if ($column->autoIncrement) continue;
      $columns[] = "?";
    }
    $sql[] = implode(",", $columns);
    $sql[] = ");";

    $compiledSql = implode("\n", $sql);

    $params = [];
    $autoIncrementPropertyName = null;

    $reflector = new \ReflectionClass(get_class($obj));
    $properties = $reflector->getProperties();
    $it = new IteratorImpl($properties);
    while ($it->hasNext()) {
      $property = $it->next();

      if (isset($meta->columns[$property->getName()]) === false) {
        continue;
      }

      $column = $meta->columns[$property->getName()];
      if ($column->autoIncrement) {
        $autoIncrementPropertyName = $property->getName();
        continue;
      }

      $reflector = new \ReflectionProperty($obj, $property->getName());
      $value     = $reflector->getValue($obj);
      $value     = $value !== null
        ? ($column->dataType == "int" ? intval($value) : $value)
        : ($column->dataType == "int" ? ($column->default === null ? 0 : intval($column->default)) : $column->default);

      $reflector->setValue($obj, $value);

      $params[] = $value;
    }

    if ($autoIncrementPropertyName === null) {
      throw new AutoIncrementPropertyNotFoundException();
    }

    $this->connection->executeNativeQuery($compiledSql, $params);
    $id = $this->connection->id();

    $reflector = new \ReflectionProperty($obj, $autoIncrementPropertyName);
    $reflector->setValue($obj, (int)$id);

    return $obj;
  }

  private function doUpdate($obj)
  {
    $meta = $this->getMeta($obj);

    $sql = [];
    $sql[] = "update `" . $meta->tableName . "`";
    $sql[] = "set";

    $columns = [];
    $it = new IteratorImpl($obj->modified);
    while ($it->hasNext()) {
      $modified = $it->next();

      if (isset($meta->columns[$modified]) === false) {
        continue;
      }

      $column = $meta->columns[$modified];
      if ($column->autoIncrement) {
        continue;
      }

      $columns[] = sprintf("`%s`=?", $column->name);
    }
    $sql[] = implode(",\n", $columns);

    $params = [];

    $reflector = new \ReflectionClass(get_class($obj));
    $it = new IteratorImpl($obj->modified);
    while ($it->hasNext()) {
      $modified = $it->next();

      if (isset($meta->columns[$modified]) === false) {
        continue;
      }

      $column = $meta->columns[$modified];
      if ($column->autoIncrement) {
        continue;
      }

      $reflector = new \ReflectionProperty($obj, $modified);
      $value     = $reflector->getValue($obj);
      $value     = $value !== null
        ? ($column->dataType == "int" ? intval($value) : $value)
        : ($column->dataType == "int" ? ($column->default === null ? 0 : intval($column->default)) : $column->default);

      $reflector->setValue($obj, $value);

      $params[] = $value;
    }

    $id = $this->getId($obj);

    if ($id === null) {
      throw new AutoIncrementPropertyNotFoundException();
    }

    $sql[] = sprintf("where `%s`=?;", $id);
    $compiledSql = implode("\n", $sql);

    $reflector = new \ReflectionProperty($obj, $id);
    $params[] = $reflector->getValue($obj);

    $this->connection->executeNativeQuery($compiledSql, $params);

    $obj->modified = [];

    return $obj;
  }

  private function getId($obj)
  {
    $meta = $this->getMeta($obj);
    $reflector = new \ReflectionClass(get_class($obj));
    $properties = $reflector->getProperties();
    $it = new IteratorImpl($properties);
    while ($it->hasNext()) {
      $property = $it->next();
      $column = $meta->columns[$property->getName()];
      if ($column->autoIncrement) {
        return $property->getName();
      }
    }

    return null;
  }

  private function doDelete($obj)
  {
    $meta = $this->getMeta($obj);

    $sql = [];
    $sql[] = "delete from `" . $meta->tableName . "`";

    $params = [];
    $autoIncrementPropertyName = null;

    $reflector = new \ReflectionClass(get_class($obj));
    $properties = $reflector->getProperties();
    $it = new IteratorImpl($properties);
    while ($it->hasNext()) {
      $property = $it->next();

      if (isset($meta->columns[$property->getName()]) === false) {
        continue;
      }

      $column = $meta->columns[$property->getName()];
      if ($column->autoIncrement) {
        $autoIncrementPropertyName = $property->getName();
        continue;
      }
    }

    if ($autoIncrementPropertyName === null) {
      throw new AutoIncrementPropertyNotFoundException();
    }

    $sql[] = sprintf("where `%s`=?;", $autoIncrementPropertyName);
    $compiledSql = implode("\n", $sql);

    $reflector = new \ReflectionProperty($obj, $autoIncrementPropertyName);
    $params[] = $reflector->getValue($obj);

    $this->connection->executeNativeQuery($compiledSql, $params);
  }

  public static function isDigit($value)
  {
    return preg_match('/^\d+$/i', $value);
  }

  /**
   * @return string
   */
  private function compileQuery($query, array $params)
  {
    return preg_replace_callback('/@([a-z_]+)/i', function ($matches) use ($params) {
      $value  = $params[$matches[1]];
      return DaoMapper::isDigit($value) ? $value : ($value === null ? "null" : "'" . $value . "'");
    }, $query);
  }

  private function getEmitValue($value)
  {
    if (preg_match('/^(?<class>[^\[]+)\[\]$/', $value, $matches)) {
      return new EmitValueArray($matches['class']);
    } else {
      return new EmitValue($value);
    }
  }
}
