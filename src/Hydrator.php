<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

use Ngexp\Hydrator\Adapters\HydrationAdapter;
use Ngexp\Hydrator\Hydrators\ClassType;
use Ngexp\Hydrator\Traits\Reflection;
use RuntimeException;

class Hydrator
{
  use Reflection;

  private ResolvedProperties $resolvedProperties;

  /**
   * @param class-string $className
   */
  public function __construct(private readonly string $className)
  {
    if (!class_exists($className)) {
      throw new RuntimeException("Class $className does not exist");
    }

    $this->resolvedProperties = $this->resolveProperties($className);
  }

  /**
   * @throws \Ngexp\Hydrator\HydratorException
   */
  public function hydrate(HydrationAdapter $adapter): object
  {
    $context = new Context(null, $adapter->getHydrationData());
    $classType = new ClassType($this->className);
    $classType->setResolvedProperties($this->resolvedProperties);
    $context = $classType->hydrateValue($context);
    if (!$context->isValid()) {
      throw new HydratorException("Hydration failed.", $context->getFailureMessages());
    }

    return $context->getValue();
  }
}
