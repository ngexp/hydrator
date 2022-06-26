<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IConstraintAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class NotEmpty extends MessageHandler implements IConstraintAttribute
{
  const IS_EMPTY = "NotEmpty::IS_EMPTY";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::IS_EMPTY => "The \"{propertyName}\" property cannot be empty, got {value}.",
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
    $result = match($context->getValueType()) {
      "string" => strlen($value) > 0,
      "array" => count($value) > 0,
      default => false
    };
    if (!$result) {
      return $context->withFailure($this->template(self::IS_EMPTY));
    }

    return $context->asValid();
  }
}
