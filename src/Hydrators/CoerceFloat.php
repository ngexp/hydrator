<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CoerceFloat implements IHydratorAttribute
{
  public function __construct()
  {
  }

  public function hydrateValue(Context $context): Context
  {
    $result = filter_var($context->getValue(), FILTER_VALIDATE_FLOAT);
    if ($result === false) {
      return $context->withError(ErrorCode::COERCE, ['type' => Type::FLOAT]);
    }

    return $context->withValue($result);
  }
}
