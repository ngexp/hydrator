<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Min implements IHydratorAttribute
{
 /**
   * @param int         $min
   * @param string|null $message Custom error message
   * @param string $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly int $min = 0,
                              private readonly ?string $message = null,
                              private readonly string $errorCode = ErrorCode::SMALL)
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_int($value) && !is_float($value)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ["type" => "int|float"]);
    }
    if ($value < $this->min) {
      if ($this->message) {
        return $context->withErrorMessage($this->message, ["min" => $this->min]);
      }
      return $context->withError($this->errorCode, ["min" => $this->min]);
    }

    return $context->asValid();
  }
}
