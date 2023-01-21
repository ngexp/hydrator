<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\MessageHandler;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CustomHydrator extends MessageHandler implements IHydratorAttribute
{
  const NOT_A_CLASS = "CustomHydrator::NOT_A_CLASS";
  const NOT_INVOKABLE = "CustomHydrator::NOT_INVOKABLE";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_A_CLASS => "{className} does not exist.",
    self::NOT_INVOKABLE => "{className} does not have an invokable method.",
  ];

  /**
   * @param array<string, string> $messageTemplates
   */
  public function __construct(private readonly string $className, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function hydrateValue(Context $context): Context
  {
    if (! class_exists($this->className)) {
      return $context->withFailure($this->template(self::NOT_A_CLASS), ['className' => $this->className]);
    }
    $hydrator = new $this->className;
    if (! is_callable($hydrator)) {
      return $context->withFailure($this->template(self::NOT_INVOKABLE), ['className' => $this->className]);
    }

    return $hydrator($context);
  }
}
