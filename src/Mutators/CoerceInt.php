<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Mutators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CoerceInt implements IHydratorAttribute
{
  public function process(Context $context): Context
  {
    $result = filter_var($context->getValue(), FILTER_VALIDATE_INT);
    if ($result === false) {
      return $context->withError(ErrorCode::COERCE, ['type' => Type::INT]);
    }

    return $context->withValue($result);
  }
}
