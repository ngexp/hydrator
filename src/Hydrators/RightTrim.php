<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class RightTrim extends MessageHandler implements IHydratorAttribute
{
  const NOT_A_STRING = "RightTrim::NOT_A_STRING";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_A_STRING => "The \"{propertyName}\" property is not of type string, could not right trim string.",
  ];

  /**
   * @param string                $characters
   * @param array<string, string> $messageTemplates
   */
  public function __construct(private readonly string $characters = " \t\n\r\0\x0B", array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function hydrateValue(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withFailure($this->template(self::NOT_A_STRING));
    }

    return $context->withValue(rtrim($value, $this->characters));
  }
}
