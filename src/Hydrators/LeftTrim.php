<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class LeftTrim implements IHydratorAttribute
{
  /**
   * @param string                $characters
   */
  public function __construct(private readonly string $characters = " \t\n\r\0\x0B")
  {
  }

  public function hydrateValue(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withError(ErrorCode::STRING);
    }

    return $context->withValue(ltrim($value, $this->characters));
  }
}
