<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Pattern implements IHydratorAttribute
{
  /**
   * @param string      $pattern
   * @param string|null $message Custom error message
   * @param string $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(public string $pattern,
                              private readonly ?string $message = null,
                              private readonly string $errorCode = ErrorCode::MATCH)
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ["typ" => Type::STRING]);
    }
    if (!preg_match($this->pattern, $value)) {
      if ($this->message) {
        return $context->withErrorMessage($this->message, ["pattern" => $this->pattern]);
      }
      return $context->withError($this->errorCode ?: ErrorCode::MATCH, ["pattern" => $this->pattern]);
    }

    return $context->asValid();
  }
}
