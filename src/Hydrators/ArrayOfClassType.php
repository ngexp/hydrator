<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\Traits\Reflection;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class ArrayOfClassType implements IHydratorAttribute
{
  use Reflection;

  /**
   * @param class-string $className
   */
  public function __construct(private readonly string $className)
  {
  }

  public function hydrateValue(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_array($value)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ["type" => "array"]);
    }

    $resolvedProperties = $this->resolveProperties($this->className);

    $result = [];
    foreach ($value as $item) {
      $classType = new ClassType($this->className);
      $classContext = new Context(null, $item, $classType);
      $classType->setResolvedProperties($resolvedProperties);
      $classContext = $classType->hydrateValue($classContext);
      if (!$classContext->isValid()) {
        $context->inheritState($classContext);
        return $context;
      }
      $result[] = $classContext->getValue();
    }

    return $context->withValue($result);
  }
}
