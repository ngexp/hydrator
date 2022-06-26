<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CoerceFloat extends MessageHandler implements IHydratorAttribute
{
  const INVALID_TYPE = 'CoerceFloat::INVALID_TYPE';

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::INVALID_TYPE => "The \"{propertyName}\" property cannot be coerced to a float type, got value {value}."
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
    $result = filter_var($context->getValue(), FILTER_VALIDATE_FLOAT);
    if ($result === false) {
      return $context->withFailure($this->template(self::INVALID_TYPE));
    }

    return $context->withValue($result);
  }
}
