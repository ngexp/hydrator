<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Digit extends MessageHandler implements IConstraintAttribute
{
  const NOT_DIGIT = "Alpha::NOT_DIGIT";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_DIGIT => "The \"{propertyName}\" property must contain a valid numeric string value, got {value}."
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
    $result = ctype_digit($context->getValue());
    if (!$result) {
      return $context->withFailure($this->template(self::NOT_DIGIT));
    }

    return $context->asValid();
  }
}
