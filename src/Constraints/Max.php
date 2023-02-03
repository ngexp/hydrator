<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Max implements IHydratorAttribute
{
  /**
   * @param int         $max
   * @param string|null $message Custom error message
   * @param string|null $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly int $max = 0,
                              private readonly ?string $message = null,
                              private readonly ?string $errorCode = null)
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_int($value) && !is_float($value)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ["type" => "int|float"]);
    }
    if ($value > $this->max) {
      if ($this->message) {
        return $context->withErrorMessage($this->message, ["max" => $this->max]);
      }
      return $context->withError($this->errorCode ?: ErrorCode::LARGE, ["max" => $this->max]);
    }

    return $context->asValid();
  }
}
