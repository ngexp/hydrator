<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Alpha extends MessageHandler implements IConstraintAttribute
{
  const NOT_ALPHA = "Alpha::NOT_ALPHA";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_ALPHA => "The \"{propertyName}\" property must have a valid alphabetic string value, got {value}."
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
    $result = ctype_alpha($context->getValue());
    if (!$result) {
      return $context->withFailure($this->useTemplate(self::NOT_ALPHA));
    }

    return $context->asValid();
  }
}
