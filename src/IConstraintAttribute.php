<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator;

interface IConstraintAttribute
{
  public function constraint(Context $context): Context;
}
