<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator;

use Ngexp\Hydrator\Adapters\HydrationAdapter;
use Ngexp\Hydrator\Mutators\ClassType;
use Ngexp\Hydrator\Traits\Reflection;

/**
 * @template T of object
 */
class Hydrator
{
  use Reflection;

  private ResolvedProperties $resolvedProperties;

  /**
   * @param class-string<T>       $className
   * @param array<string, string> $customErrorMessages
   */
  public function __construct(private readonly string $className, private readonly array $customErrorMessages = [])
  {
    if (!class_exists($className)) {
      throw new RuntimeHydrationException("Class $className does not exist");
    }

    $this->resolvedProperties = $this->resolveProperties($className);
  }

  /**
   * @param \Ngexp\Hydrator\Adapters\HydrationAdapter $adapter
   *
   * @return T
   * @throws \Ngexp\Hydrator\HydratorException
   */
  public function hydrate(HydrationAdapter $adapter): object
  {
    $classType = new ClassType($this->className);
    $context = new Context(null, $adapter->getHydrationData(), $classType);
    $classType->setResolvedProperties($this->resolvedProperties);
    $context = $classType->process($context);
    if (!$context->isValid()) {
      $errorMessages = new ErrorMessageAggregate($context->getErrors(), $this->customErrorMessages);
      throw new HydratorException(
        $errorMessages->first(), $context->getErrors(), $errorMessages
      );
    }

    /** @var T $object */
    $object = $context->getValue();
    return $object;
  }
}
