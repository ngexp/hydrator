<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Asserts;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class NotBlank implements IHydratorAttribute
{
  /**
   * @param string|null $message Custom error message
   * @param string $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly ?string $message = null,
                              private readonly string $errorCode = ErrorCode::BLANK)
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ["type" => Type::STRING]);
    }

    $trimmed = trim($value);
    if (strlen($trimmed) === 0) {
      if ($this->message) {
        return $context->withErrorMessage($this->message);
      }
      return $context->withError($this->errorCode ?: ErrorCode::BLANK);
    }

    return $context->asValid();
  }
}
