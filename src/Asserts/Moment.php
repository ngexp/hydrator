<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Asserts;

use Attribute;
use DateTime;
use DateTimeZone;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Moment implements IHydratorAttribute
{
  /**
   * @param string             $dateFormat
   * @param string|null        $message   Custom error message
   * @param string             $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(private readonly string $dateFormat = "Y-m-d",
                              private readonly ?string $message = null,
                              private readonly string $errorCode = ErrorCode::DATE)
  {
  }
  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (! is_string($value)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ["type" => Type::STRING]);
    }

    $date = DateTime::createFromFormat($this->dateFormat, $value);
    $valid = $date && $date->format($this->dateFormat) == $value;

    if (!$valid) {
      if ($this->message) {
        return $context->withErrorMessage($this->message);
      }
      return $context->withError($this->errorCode, ["format" => $this->dateFormat]);
    }

    return $context->asValid();
  }
}
