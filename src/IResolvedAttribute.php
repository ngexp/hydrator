<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator;

interface IResolvedAttribute
{
  function setResolvedProperties(ResolvedProperties $resolvedProperties): void;
}
