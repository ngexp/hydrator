<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Optional extends MessageHandler implements IConstraintAttribute
{
  public function constraint(Context $context): Context
  {
    return $context->asValid();
  }
}
