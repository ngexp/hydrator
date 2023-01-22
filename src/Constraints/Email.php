<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Email extends MessageHandler implements IConstraintAttribute
{
  const NOT_EMAIL = "Email::NOT_EMAIL";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_EMAIL => "The \"{propertyName}\" property must contain a valid email address as a string value, got {value}."
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
    $result = filter_var($context->getValue(), FILTER_VALIDATE_EMAIL);
    if ($result === false) {
      return $context->withFailure($this->useTemplate(self::NOT_EMAIL));
    }

    return $context->asValid();
  }
}
