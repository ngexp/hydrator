<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class AutoCast extends MessageHandler implements IHydratorAttribute
{
  const AUTO_FAIL = "AutoCast::AUTO_FAIL";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::AUTO_FAIL => "The \"{propertyName}\" properties type {expectedType}, can not be auto casted.",
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
    $expectedType = $context->getExpectedType();
    switch ($expectedType) {
      case Type::BOOL:
        $hydrate = new CoerceBool();
        return $hydrate->hydrateValue($context);

      case Type::FLOAT:
        $hydrate = new CoerceFloat();
        return $hydrate->hydrateValue($context);

      case Type::INT:
        $hydrate = new CoerceInt();
        return $hydrate->hydrateValue($context);

      case Type::STRING:
        $hydrate = new CoerceString();
        return $hydrate->hydrateValue($context);

      default:
        return $context->withFailure($this->template(self::AUTO_FAIL));
    }
  }
}
