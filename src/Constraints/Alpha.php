<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Alpha implements IConstraintAttribute
{
  const NOT_ALPHA = "Alpha::NOT_ALPHA";

  /**
   * @param string|null $message Custom error message
   * @param string|null $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly ?string $message = null, private readonly ?string $errorCode = null)
  {
  }

  public function constraint(Context $context): Context
  {
    $result = ctype_alpha($context->getValue());
    if (!$result) {
      if ($this->message) {
        return $context->withErrorMessage($this->message);
      }
      return $context->withError($this->errorCode ?: ErrorCode::ALPHA);
    }

    return $context->asValid();
  }
}
