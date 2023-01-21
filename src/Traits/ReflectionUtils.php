<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Traits;

use Ngexp\Hydrator\Type;
use RuntimeException;

trait ReflectionUtils
{
  /**
   * @param mixed $variable
   *
   * @return string
   */
  protected function getVariableType(mixed $variable): string
  {
    $type = gettype($variable);
    return match ($type) {
      "boolean" => Type::BOOL,
      "integer" => Type::INT,
      "double" => Type::FLOAT,
      "string" => Type::STRING,
      "array" => Type::ARRAY,
      "object" => get_class($variable) ?: Type::OBJECT,
      "NULL" => Type::NULL,
      default => throw new RuntimeException("Unknown type"),
    };
  }
}
