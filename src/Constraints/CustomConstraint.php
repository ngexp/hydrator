<?php

declare(strict_types=1);


namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\MessageHandler;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CustomConstraint extends MessageHandler implements IConstraintAttribute
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

  public function constraint(Context $context): Context
  {
    if (! class_exists($this->className)) {
      return $context->withFailure($this->useTemplate(self::NOT_A_CLASS), ['className' => $this->className]);
    }
    $constraint = new $this->className;
    if (! is_callable($constraint)) {
      return $context->withFailure($this->useTemplate(self::NOT_INVOKABLE), ['className' => $this->className]);
    }

    return $constraint($context);
  }
}
