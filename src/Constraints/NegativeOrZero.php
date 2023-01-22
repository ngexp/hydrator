<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class NegativeOrZero extends MessageHandler implements IConstraintAttribute
{
  const NOT_A_NUMBER = "NegativeOrZero::NOT_A_NUMBER";
  const NOT_NEGATIVE_OR_ZERO = "NegativeOrZero::NOT_NEGATIVE_OR_ZERO";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_A_NUMBER => "The value type for the \"{propertyName}\" property must be of type int or float, got {valueType}.",
    self::NOT_NEGATIVE_OR_ZERO => "The \"{propertyName}\" property must contain a negative number or zero, got {value}."
  ];

  /**
   * @param array<string, string> $messageTemplates
   */
  public function __construct(array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function constraint(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_int($value) && !is_float($value)) {
      $context->withFailure($this->useTemplate(self::NOT_A_NUMBER));
    }
    if ($value > 0) {
      return $context->withFailure($this->useTemplate(self::NOT_NEGATIVE_OR_ZERO));
    }

    return $context->asValid();
  }
}
