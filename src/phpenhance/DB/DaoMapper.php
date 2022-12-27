<?php

namespace phpenhance\DB;

use phpenhance\Annotations\AnnotationReader;
use phpenhance\Util\IteratorImpl;

class DaoMapper
{
  private $connection;
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
      $fromArrayMethod = $emitValueClassReflector->getMethod("fromArray");
      if ($fromArrayMethod === null) {
        throw new \Exception("Emit class must have a method fromArray()");
      }

      if ($emitValue instanceof EmitValueArray) {
        $list = $st->fetchAll(\PDO::FETCH_ASSOC);

        $models = [];

        $it = new IteratorImpl($list);
        while ($it->hasNext()) {
          $arr = $it->next();

          $models[] = $fromArrayMethod->invoke(null, $arr);
        }

        return $models;
      } else {
        $arr = $st->fetch(\PDO::FETCH_ASSOC);
        return $fromArrayMethod->invoke(null, $arr);
      }
    } else if ($insertAnnotation !== null) {
      
    } else if ($updateAnnotation !== null) {

    } else if ($deleteAnnotation !== null) {

    }
  }

  public static function isDigit($value)
  {
    return preg_match('/^\d+$/i', $value);
  }

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
