<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class AutoCast implements IHydratorAttribute
{
  public function __construct()
  {
  }

  public function hydrateValue(Context $context): Context
  {
    $expectedType = $context->getExpectedType();
    switch ($expectedType) {
      case Type::BOOL:
        $hydrate = new CoerceBool();
        return $hydrate->hydrateValue($context);

      case Type::FLOAT:
        $hydrate = new CoerceFloat();
        return $hydrate->hydrateValue($context);

      case Type::INT:
        $hydrate = new CoerceInt();
        return $hydrate->hydrateValue($context);

      case Type::STRING:
        $hydrate = new CoerceString();
        return $hydrate->hydrateValue($context);

      case Type::MIXED:
        return $context->asValid();

      default:
        return $context->withError(ErrorCode::AUTO);
    }
  }
}
