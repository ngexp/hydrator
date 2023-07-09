<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Mutators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class AutoCast implements IHydratorAttribute
{
  public function process(Context $context): Context
  {
    $expectedType = $context->getExpectedType();
    switch ($expectedType) {
      case Type::BOOL:
        $hydrate = new CoerceBool();
        return $hydrate->process($context);

      case Type::FLOAT:
        $hydrate = new CoerceFloat();
        return $hydrate->process($context);

      case Type::INT:
        $hydrate = new CoerceInt();
        return $hydrate->process($context);

      case Type::STRING:
        $hydrate = new CoerceString();
        return $hydrate->process($context);

      case Type::MIXED:
        return $context->asValid();

      default:
        return $context->withError(ErrorCode::AUTO);
    }
  }
}
