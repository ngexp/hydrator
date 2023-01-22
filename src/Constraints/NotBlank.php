<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class NotBlank extends MessageHandler implements IConstraintAttribute
{
  const IS_BLANK = "NotBlank::IS_BLANK";
  const NOT_A_STRING = "NotBlank::NOT_A_STRING";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_A_STRING => "The \"{propertyName}\" property value is not of type string.",
    self::IS_BLANK => "The \"{propertyName}\" property cannot be empty or contain whitespaces only, got {value}."
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
    if (!is_string($value)) {
      return $context->withFailure($this->useTemplate(self::NOT_A_STRING));
    }

    $trimmed = trim($value);
    if (strlen($trimmed) === 0) {
      return $context->withFailure($this->useTemplate(self::IS_BLANK));
    }

    return $context->asValid();
  }
}
