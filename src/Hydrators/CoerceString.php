<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CoerceString extends MessageHandler implements IHydratorAttribute
{
  const INVALID_TYPE = 'CoerceString::INVALID_TYPE';

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::INVALID_TYPE => "The \"{propertyName}\" property cannot be coerced to a string type, got value {value}."
  ];

  /**
   * @param array<string, string> $messageTemplates
   */
  public function __construct(array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function hydrateValue(Context $context): Context
  {
    $value = $context->getValue();
    if (is_array($value) || is_object($value)) {
      return $context->withFailure($this->useTemplate(self::INVALID_TYPE));
    }

    return $context->withValue(strval($value));
  }
}
