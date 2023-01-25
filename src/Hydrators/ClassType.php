<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\IResolvedAttribute;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\ResolvedProperties;
use Ngexp\Hydrator\ResolvedProperty;
use Ngexp\Hydrator\Traits\Reflection;
use Ngexp\Hydrator\Type;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class ClassType implements IHydratorAttribute, IResolvedAttribute
{
  use Reflection;

  private readonly string $uid;
  private ?ResolvedProperties $resolvedProperties = null;

  /**
   * @param class-string $className
   */
  public function __construct(private readonly string $className)
  {
    $this->uid = uniqid();
  }

  public function hydrateValue(Context $context): Context
  {
    // If we didn't get the resolved properties then resolve them now. This usually happens on depth calls, while
    // we pre-resolve on shallow classes for speed.
    if (!$this->resolvedProperties) {
      $this->resolvedProperties = $this->resolveProperties($this->className);
    }
    $classInstance = $this->createClassInstance($this->className);
    $hydrationData = $context->getValue();

    // A class must always have hydration data in form of an array.
    if (!is_array($hydrationData)) {
      return $context->withError(ErrorCode::INVALID_TYPE, ['type' => $this->className]);
    }

    foreach ($this->resolvedProperties->getProperties() as $property) {
      $propertyName = $property->getPropertyName();
      $value = $hydrationData[$propertyName] ?? null;
      $propContext = new Context($property, $value, $this);
      $propContext->setParentContext($context);

      // Check if we have the data to hydrate the property with.
      if (!array_key_exists($propertyName, $hydrationData)) {
        if (!$property->isOptional()) {
          $propContext->withError(ErrorCode::REQUIRED);
          $context->inheritState($propContext);
        }
        continue;
      }

      // If the value is not null, go through all hydration attributes for the property and update the value.
      // NULL values are checked against the expected type later, if it's nullable or not.
      if ($propContext->getValueType() !== Type::NULL) {
        $propContext = $this->runHydrators($propContext);
        if (!$propContext->isValid()) {
          $context->inheritState($propContext);
          continue;
        }
      }

      // Now do a final type check of the value against the expected type. This is done after the hydrators
      // has run, since they might have changed its type.
      $propContext = $this->verifyTypeMatch($propContext);
      if (!$propContext->isValid()) {
        $context->inheritState($propContext);
        continue;
      }

      // Now go through all constraints attributes and verify that the value pass all the checks.
      $propContext = $this->runConstraints($propContext);
      if (!$propContext->isValid()) {
        $context->inheritState($propContext);
        continue;
      }

      $this->setPropertyValue($classInstance, $propContext);
    }
    if (!$context->isValid()) {
      return $context;
    }

    return $context->withValue($classInstance);
  }

  /**
   * @param class-string $className
   *
   * @return object
   */
  private function createClassInstance(string $className): object
  {
    try {
      $reflectionClass = new ReflectionClass($className);
      return $reflectionClass->newInstanceWithoutConstructor();
    } catch (ReflectionException $e) {
      throw new RuntimeException($e->getMessage());
    }
  }

  private function runHydrators(Context $context): Context
  {
    // This is a hack to get classType in here, ClassType is working as an attribute, but is never set directly on
    // a prop that is already type defined.
    if ($context->isClassProperty()) {
      $classType = new ClassType($context->getClassName());
      $context = $classType->hydrateValue($context);
      if (!$context->isValid()) {
        return $context;
      }
    }

    foreach ($context->getProperty()->getAttributes() as $attribute) {
      $instance = $attribute->newInstance();
      if (!($instance instanceof IHydratorAttribute)) {
        continue;
      }

      $context = $instance->hydrateValue($context);
      if (!$context->isValid()) {
        return $context;
      }
    }
    return $context->asValid();
  }

  private function runConstraints(Context $context): Context
  {
    foreach ($context->getProperty()->getAttributes() as $attribute) {
      $instance = $attribute->newInstance();
      if (!($instance instanceof IConstraintAttribute)) {
        continue;
      }

      $context = $instance->constraint($context);
      if (!$context->isValid()) {
        return $context;
      }
    }
    return $context->asValid();
  }

  private function verifyTypeMatch(Context $context): Context
  {
    $expectedType = $context->getExpectedType();
    $actualType = $context->getValueType();

    if ($actualType === Type::NULL) {
      if ($context->getProperty()->allowsNull()) {
        return $context->asValid();
      }
      return $context->withError(ErrorCode::NULL);
    }

    if ($context->getProperty()->isEnum()) {
      $value = $context->getProperty()->resolveEnumCase($context->getValue());
      if (!$value) {
        return $context->withError(ErrorCode::ENUM);
      }
      $context->setValue($value);
      return $context->asValid();
    }

    if (!$context->getProperty()->hasType($actualType)) {
        return $context->withError(ErrorCode::EXPECTED_TYPE);
    }
    return $context->asValid();
  }

  private function setPropertyValue(mixed $classInstance, Context $context): void
  {
    $resolvedProperty = $context->getProperty();
    $value = $context->getValue();

    // The value is either set directly to the property or via a setter method.
    if ($resolvedProperty->getSetBy() === ResolvedProperty::SET_BY_PROPERTY) {
      $classInstance->{$resolvedProperty->getPropertyName()} = $value;
    } elseif ($resolvedProperty->getSetBy() === ResolvedProperty::SET_BY_METHOD) {
      $classInstance->{$resolvedProperty->getName()}($value);
    }
  }

  function setResolvedProperties(ResolvedProperties $resolvedProperties): void
  {
    $this->resolvedProperties = $resolvedProperties;
  }

  public function getUid(): string
  {
    return $this->uid;
  }

  public function getClassName(): string
  {
    return $this->className;
  }

  public function equal(ClassType $classType): bool
  {
    return $this->uid === $classType->getUid();
  }
}
