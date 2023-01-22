<?php

declare(strict_types=1);


namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IConstraintAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class CustomConstraint implements IConstraintAttribute
{
  /**
   * @param string $className
   */
  public function __construct(private readonly string $className)
  {
  }

  public function constraint(Context $context): Context
  {
    if (!class_exists($this->className)) {
      return $context->withError(ErrorCode::CLASS_NAME, ['className' => $this->className]);
    }
    $constraint = new $this->className;
    if (!is_callable($constraint)) {
      return $context->withError(ErrorCode::INVOKABLE, ['className' => $this->className]);
    }

    return $constraint($context);
  }
}
