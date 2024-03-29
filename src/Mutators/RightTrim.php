<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Mutators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class RightTrim implements IHydratorAttribute
{
  /**
   * @param string $characters Characters that should be trimmed.
   */
  public function __construct(private readonly string $characters = " \t\n\r\0\x0B")
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ["type" => Type::STRING]);
    }

    return $context->withValue(rtrim($value, $this->characters));
  }
}
