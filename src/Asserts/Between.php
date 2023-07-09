<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Asserts;

use Attribute;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Between implements IHydratorAttribute
{
  /**
   * @param int         $min
   * @param int         $max
   * @param string|null $message   Custom error message
   * @param string      $errorCode Custom error code, will be ignored if message is not null.
   */
  public function __construct(public int               $min = 0,
                              public int               $max = PHP_INT_MAX,
                              private readonly ?string $message = null,
                              private readonly string  $errorCode = ErrorCode::BETWEEN)
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    $type = $context->getValueType();

    switch ($type) {
      case Type::STRING:
        /** @phpstan-ignore-next-line */
        if (strlen($value) < $this->min || strlen($value) > $this->max) {
          if ($this->message) {
            return $context->withErrorMessage($this->message);
          }
          return $context->withError($this->errorCode ?: ErrorCode::BETWEEN);
        }
        break;

      case Type::ARRAY:
        /** @phpstan-ignore-next-line */
        if (count($value) < $this->min || count($value) > $this->max) {
          if ($this->message) {
            return $context->withErrorMessage($this->message);
          }
          return $context->withError($this->errorCode ?: ErrorCode::BETWEEN);
        }
        break;

      default:
        return $context->withError(ErrorCode::INVALID_TYPE, ["type" => "array or string"]);
    }

    return $context->asValid();
  }
}
