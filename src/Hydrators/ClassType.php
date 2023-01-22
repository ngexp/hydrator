<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\IResolvedAttribute;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\ResolvedProperties;
use Ngexp\Hydrator\ResolvedProperty;
use Ngexp\Hydrator\Traits\Reflection;
use Ngexp\Hydrator\Type;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class ClassType extends MessageHandler implements IHydratorAttribute, IResolvedAttribute
{
  use Reflection;

  const CLASS_ERROR = "ClassType::CLASS_ERROR";
  const PROP_ERROR = "ClassType::PROP_ERROR";
  const REQUIRED_PROP = "ClassType::REQUIRED_PROP";
  const EXPECTED_TYPE = "ClassType::EXPECTED_TYPE";
  const NOT_NULL = "ClassType::NOT_NULL";

  private ?ResolvedProperties $resolvedProperties = null;

  protected array $messageTemplates = [
    self::CLASS_ERROR => "Error in the instance of {classType}.",
    self::PROP_ERROR => "Error in the property \"{propertyName}\" of type \"{classType}\".",
    self::REQUIRED_PROP => "Required value for the \"{propertyName}\" property is missing.",
    self::EXPECTED_TYPE => "The \"{propertyName}\" property expected a value of type {expectedType}, got a value of type {valueType}.",
    self::NOT_NULL => "The \"{propertyName}\" property expected non nullable type {expectedType}, got nul.",
  ];

  /**
   * @param class-string          $className
   * @param array<string, string> $messageTemplates
   */
  public function __construct(private readonly string $className, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function hydrateValue(Context $context): Context
  {
    // If we don't get the resolved properties then resolve them now. This usually happens on depth calls, while
    // we pre-resolve on shallow classes for speed.
    if (!$this->resolvedProperties) {
      $this->resolvedProperties = $this->resolveProperties($this->className);
    }
    $classInstance = $this->createClassInstance($this->className);
    $hydrationData = $context->getValue();

    foreach ($this->resolvedProperties->getProperties() as $property) {
      $propertyName = $property->getPropertyName();
      $value = $hydrationData[$propertyName] ?? null;
      $propContext = new Context($property, $value);

      // Check if we have the data to hydrate the property with.
      if (!array_key_exists($propertyName, $hydrationData)) {
        if (!$property->isOptional()) {
          $propContext->withFailure($this->useTemplate(self::REQUIRED_PROP));
          $context->inheritFailState($propContext);
        }
        continue;
      }

      // If the value is not null, go through all hydration attributes for the property and update the value.
      // NULL values are checked against the expected type later, if it's nullable or not.
      if ($propContext->getValueType() !== Type::NULL) {
        $propContext = $this->runHydrators($propContext);
        if (!$propContext->isValid()) {
          $context->inheritFailState($propContext);
          continue;
        }
      }

      // Now do a final type check of the value against the expected type. This is done after the hydrators
      // has run, since they might have changed its type.
      $propContext = $this->verifyTypeMatch($propContext);
      if (!$propContext->isValid()) {
        $context->inheritFailState($propContext);
        continue;
      }

      // Now go through all constraints attributes and verify that the value pass all the checks.
      $propContext = $this->runConstraints($propContext);
      if (!$propContext->isValid()) {
        $context->inheritFailState($propContext);
        continue;
      }

      $this->setPropertyValue($classInstance, $propContext);
    }
    if (!$context->isValid()) {
      if ($context->hasProperty()) {
        return $context->withMainFailure($this->useTemplate(self::PROP_ERROR), ["classType" => $this->className]);
      } else {
        return $context->withMainFailure($this->useTemplate(self::CLASS_ERROR), ["classType" => $this->className]);
      }
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

    if ($expectedType !== Type::MIXED && $actualType !== $expectedType) {
      // NULL is returned as a type, verify if the property allows null.
      if ($actualType === Type::NULL) {
        if (!$context->getProperty()->allowsNull()) {
          return $context->withFailure($this->useTemplate(self::NOT_NULL));
        }
      } else {
        return $context->withFailure($this->useTemplate(self::EXPECTED_TYPE));
      }
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
}
