<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Mutators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CoerceString implements IHydratorAttribute
{
  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (is_array($value) || is_object($value)) {
      return $context->withError(ErrorCode::COERCE, ['type' => Type::STRING]);
    }

    return $context->withValue(strval($value));
  }
}
