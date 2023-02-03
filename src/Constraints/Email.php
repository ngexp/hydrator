<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Email implements IHydratorAttribute
{
  /**
   * @param string|null $message Custom error message
   * @param string|null $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly ?string $message = null, private readonly ?string $errorCode = null)
  {
  }

  public function process(Context $context): Context
  {
    $result = filter_var($context->getValue(), FILTER_VALIDATE_EMAIL);
    if ($result === false) {
      if ($this->message) {
        return $context->withErrorMessage($this->message);
      }
      return $context->withError($this->errorCode ?: ErrorCode::EMAIL);
    }

    return $context->asValid();
  }
}
