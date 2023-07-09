<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Asserts;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class NegativeOrZero implements IHydratorAttribute
{
  /**
   * @param string|null $message Custom error message
   * @param string $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly ?string $message = null,
                              private readonly string $errorCode = ErrorCode::NEGATIVE)
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_int($value) && !is_float($value)) {
      $context->withError(ErrorCode::INVALID_TYPE, ["type" => "int|float"]);
    }
    if ($value > 0) {
      if ($this->message) {
        return $context->withErrorMessage($this->message);
      }
      return $context->withError($this->errorCode);
    }

    return $context->asValid();
  }
}
