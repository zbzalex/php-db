<?php

namespace phpenhance\DB;

use phpenhance\Util\IteratorImpl;

class DaoMapper
{
  private $connection;
  private $daoClass;

  public function __construct(Connection $connection, $daoClass)
  {
    $this->connection = $connection;
    $this->daoClass = $daoClass;
  }

  public function __call($method, $arguments)
  {
    $reflectionClass = new \ReflectionClass($this->daoClass);
    $reflectionMethod = $reflectionClass->getMethod($method);
    $parameters = $reflectionMethod->getParameters();

    $reader = new \Annotations\AnnotationReader($this->daoClass, $method);
    $reader->parse();

    $insertAnnotation = $this->getAnnotation($reader->getAnnotations(), "Insert");
    $updateAnnotation = $this->getAnnotation($reader->getAnnotations(), "Update");
    $deleteAnnotation = $this->getAnnotation($reader->getAnnotations(), "Delete");

    $queryAnnotation = $this->getAnnotation($reader->getAnnotations(), "Query");
    if ($queryAnnotation !== null) {
      $queryAnnotationValue = $this->getAnnotationParam($queryAnnotation->getParams(), "value");

      $emitAnnotation = $this->getAnnotation($reader->getAnnotations(),  "Emit");
      if ($emitAnnotation === null) {
        throw new \Exception("@Emit  annotation not found");
      }

      $emitAnnotationValue = $this->getAnnotationParam($emitAnnotation->getParams(), "value");
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
    if (preg_match('/^(?<class>[^\[]+)\[\]/', $value, $matches)) {
      return new EmitValueArray($matches['class']);
    } else {
      return new EmitValue($value);
    }
  }

  private function getAnnotation(array $annotations, $annotationName)
  {
    $it = new IteratorImpl($annotations);
    while ($it->hasNext()) {
      $annotation = $it->next();
      if ($annotation->getName() == $annotationName) {
        return $annotation;
      }
    }

    return null;
  }

  private function getAnnotationParam(array $params, $pname)
  {
    $it = new IteratorImpl(array_keys($params));
    while ($it->hasNext()) {
      $p = $it->next();
      if ($p == $pname) {
        return $params[$p];
      }
    }

    return null;
  }
}
