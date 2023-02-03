<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator;

interface IHydratorAttribute
{
  public function process(Context $context): Context;
}
