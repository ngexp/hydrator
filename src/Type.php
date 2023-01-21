<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

enum Type: string
{
  const BOOL = "bool";
  const INT = "int";
  const FLOAT = "float";
  const STRING = "string";
  const ARRAY = "array";
  const NULL = "NULL";
  const OBJECT = "object";
  const MIXED = "mixed";
}
