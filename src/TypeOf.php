<?php

namespace Ngexp\Hydrator;

enum TypeOf
{
  case ClassType;
  case EnumType;
  case IntersectionType;
  case NullType;
  case ScalarType;
  case UnionType;
}
