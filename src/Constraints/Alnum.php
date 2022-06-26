<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Alnum extends MessageHandler implements IConstraintAttribute
{
  const NOT_ALNUM = "Alnum::NOT_ALNUM";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_ALNUM => "The \"{propertyName}\" property must contain a valid alpha numeric string value, got {value}."
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
    $result = ctype_alnum($context->getValue());
    if (!$result) {
      return $context->withFailure($this->template(self::NOT_ALNUM));
    }

    return $context->asValid();
  }
}
