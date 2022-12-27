<?php

namespace Annotations;

class AnnotationReader
{
  private $rawDocBlock;
  private $annotations = [];
  private $delimiters = "@()=,";
  private $blanks = "\x20\n\r\t";
  private $tokens = [];

  public function __construct()
  {
    $arguments = func_get_args();
    $count = count($arguments);

    $reflector = null;
    if ($count == 0) {
    } else if ($count == 1) {
      $reflector = new \ReflectionClass($arguments[0]);
    } else {
      $type = $count == 3 ? $arguments[2] : "method";
      switch ($type) {
        case "property":
          $reflector = new \ReflectionProperty($arguments[0], $arguments[1]);
          break;
        default:
        case "method":
          $reflector = new \ReflectionMethod($arguments[0], $arguments[1]);
          break;
      }
    }

    $this->rawDocBlock = $reflector->getDocComment();
    $this->annotations = [];
  }

  private function isBlank($char)
  {
    $count = strlen($this->blanks);
    $i = 0;
    do {
      if ($char == substr($this->blanks, $i, 1)) return true;
    } while ($i++ < $count - 1);

    return false;
  }

  private function isDelimiter($char)
  {
    $count = strlen($this->delimiters);
    $i = 0;
    do {
      if ($char == substr($this->delimiters, $i, 1)) return true;
    } while ($i++ < $count - 1);

    return false;
  }

  private function normalize()
  {
    $lines = explode("\n", $this->rawDocBlock);
    $line  = 0;
    $tmp   = [];
    do {
      $i = 0;
      do {
        $ch = substr($lines[$line], $i, 1);
        if ($ch != "/" && $ch != "*" && $ch != " ") break;
      } while ($i++ < strlen($lines[$line]) - 1);

      if ($i < strlen($lines[$line])) {
        $tmp[] = substr($lines[$line], $i);
      }
    } while ($line++ < count($lines) - 1);

    return implode("\n", $tmp);
  }

  public function parse()
  {
    $normDoc = $this->normalize();
    $this->tokenize($normDoc);
    $this->compile();
  }

  private function tokenize($raw)
  {
    $len = 0;
    $cursor = 0;
    $start  = 0;
    $value = null;
    $insideString = false;

    do {
      $len = $cursor - $start;
      $value = $cursor == strlen($raw) ? null : substr($raw, $cursor, 1);
      if ($value == null) break;


      if (($this->isBlank($value) || $this->isDelimiter($value)) && !$insideString) {
        if ($len > 0) {
          $this->pushToken($raw, $start, $len);
        }

        $nextChar = $cursor + 1 == strlen($raw) - 1 ? null : substr($raw, $cursor + 1, 1);
        if ($this->isDelimiter($value) || $this->isDelimiter($nextChar)) {
          $this->pushToken($raw, $cursor + 0, 1);
          // $this->pushToken($raw, $cursor + 1, 1);
        } else {
          $this->pushToken($raw, $cursor + 0, 1);
        }

        $start = $cursor + 1;
      } else if ($value == "\"") {
        $insideString = !$insideString;
      }

      $cursor++;
    } while (1);

    if ($insideString) {
      $len = $cursor - $start;
      $this->pushToken($raw, $start, $len);
    }

    $this->tokens[] = [
      "end",
      ""
    ];
  }

  private function pushToken($raw, $start, $len)
  {
    if (preg_match("/^[a-z]+/i", substr($raw, $start, $len))) {
      $this->tokens[] = [
        "id",
        substr($raw, $start, $len)
      ];
    } else if (substr($raw, $start, 1) == "\"") {
      $this->tokens[] = [
        "const_string",
        substr($raw, $start, $len)
      ];
    } else if (substr($raw, $start, 1) == "(") {
      $this->tokens[] = [
        "op_parentheses",
        substr($raw, $start, $len)
      ];
    } else if (substr($raw, $start, 1) == ")") {
      $this->tokens[] = [
        "cl_parentheses",
        substr($raw, $start, $len)
      ];
    } else if (substr($raw, $start, 1) == "=") {
      $this->tokens[] = [
        "assign",
        substr($raw, $start, $len)
      ];
    } else if (substr($raw, $start, 1) == "@") {
      $this->tokens[] = [
        "start_annotation",
        substr($raw, $start, $len)
      ];
    } else if (substr($raw, $start, 1) == ",") {
      $this->tokens[] = [
        "comma",
        substr($raw, $start, $len)
      ];
    }
  }

  private function compile()
  {
    $i = 0;
    do {
      if ($this->tokens[$i][0] == "start_annotation") {
        if ($this->tokens[$i + 1][0] != "id") {
          break;
        }

        $i++;

        $name   = $this->tokens[$i][1];
        $params = [];

        if (
          $this->tokens[$i + 1][0] == "start_annotation"
          || $this->tokens[$i + 1][0] == "end"
        ) {
          $this->annotations[] = new Annotation(
            $name,
            $params
          );
        } else if ($this->tokens[$i + 1][0] == "op_parentheses") {
          // skip open parentheses
          $i++;

          if ($this->tokens[$i + 1][0] == "cl_parentheses") {
            $this->annotations[] = new Annotation(
              $name,
              $params
            );

            $i++;
          } else {

            $comma = false;

            do {

              $i++;

              if ($this->tokens[$i][0] == "id" && (count($params) == 0 || $comma)) {

                $pname = $this->tokens[$i][1];
                $i++;

                if ($this->tokens[$i][0] != "assign") {
                  break;
                }

                $i++;

                if ($this->tokens[$i][0] !== "const_string") break;

                $pval  = $this->tokens[$i][1];

                $params[$pname] = $this->unquote($pval);

                $comma = false;
              } else if ($this->tokens[$i][0] == "const_string" && count($params) == 0) {
                $pname = "value";

                $pval  = $this->tokens[$i][1];

                $params[$pname] = $this->unquote($pval);
              }

              if ($this->tokens[$i][0] == "comma") {
                $comma = true;
                continue;
              }
            } while ($this->tokens[$i][0] !== "cl_parentheses");

            $this->annotations[] = new Annotation(
              $name,
              $params
            );
          }
        }
      }
    } while ($i++ < count($this->tokens) - 1);
  }

  private function unquote($cstr)
  {
    $cstr = substr($cstr, 1);
    $cstr = substr($cstr, 0, strlen($cstr) - 1);

    return $cstr;
  }

  public function getAnnotations()
  {
    return $this->annotations;
  }
}
