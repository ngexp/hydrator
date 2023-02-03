<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CoerceBool implements IHydratorAttribute
{
  public function process(Context $context): Context
  {
    $result = filter_var($context->getValue(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if (is_null($result)) {
      return $context->withError(ErrorCode::COERCE, ['type' => Type::BOOL]);
    }

    return $context->withValue($result);
  }
}
