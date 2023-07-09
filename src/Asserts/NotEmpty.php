<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Asserts;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class NotEmpty implements IHydratorAttribute
{
  /**
   * @param string|null $message Custom error message
   * @param string $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly ?string $message = null,
                              private readonly string $errorCode = ErrorCode::EMPTY)
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    $result = match($context->getValueType()) {
      /** @phpstan-ignore-next-line */
      Type::STRING => strlen($value) > 0,
      /** @phpstan-ignore-next-line */
      Type::ARRAY => count($value) > 0,
      default => false
    };
    if (!$result) {
      if ($this->message) {
        return $context->withErrorMessage($this->message);
      }
      return $context->withError($this->errorCode);
    }

    return $context->asValid();
  }
}
