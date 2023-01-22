<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CoerceBool extends MessageHandler implements IHydratorAttribute
{
  const INVALID_TYPE = 'CoerceBool::INVALID_TYPE';

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::INVALID_TYPE => "The \"{propertyName}\" property cannot be coerced to a bool type, got value {value}."
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
    $result = filter_var($context->getValue(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if (is_null($result)) {
      return $context->withFailure($this->useTemplate(self::INVALID_TYPE));
    }

    return $context->withValue($result);
  }
}
