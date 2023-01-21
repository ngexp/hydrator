<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Traits;

use JetBrains\PhpStorm\ArrayShape;
use Ngexp\Hydrator\Constraints\Optional;
use Ngexp\Hydrator\ResolvedProperties;
use Ngexp\Hydrator\ResolvedProperty;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;

trait Reflection
{
  #[ArrayShape([ResolvedProperty::class])]
  function resolveProperties(mixed $classInstance): ResolvedProperties
  {
    try {
      $reflectionClass = new ReflectionClass($classInstance);
      $resolvedProperties = new ResolvedProperties();

      // Find all public method names that starts with the word set.
      foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (str_starts_with($method->getName(), "set")) {
          $methodName = $method->getName();
          // A method is called set or doesn't have any parameters, ignore it.
          if (strlen($methodName) === 3 || $method->getNumberOfParameters() === 0) {
            continue;
          }

          $type = $method->getParameters()[0]->getType();
          $optional = count($method->getAttributes(Optional::class)) === 1;
          $resolvedProperties->add(
            $methodName,
            new ResolvedProperty(
              $methodName, $type, ResolvedProperty::SET_BY_METHOD, $optional, $method->getAttributes()
            )
          );
        }
      }

      // A private property and a public set method that share the same name also shares the same attributes.
      foreach ($resolvedProperties->getProperties() as $resolvedProperty) {
        $propertyName = $resolvedProperty->getPropertyName();
        $property = $reflectionClass->getProperty($propertyName);
        // Properties that has a public setter method can not also be public.
        if ($property->isPublic()) {
          throw new ReflectionException("Public property $propertyName has a setter with the same name.");
        }
        $resolvedProperty->addAttributes($property->getAttributes());
        $optional = count($property->getAttributes(Optional::class)) === 1;
        $resolvedProperty->updateOptional($optional);
      }

      // Find all public properties.
      foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
        $optional = count($property->getAttributes(Optional::class)) === 1;

        $resolvedProperties->add(
          $property->getName(),
          new ResolvedProperty(
            $property->getName(), $property->getType(), ResolvedProperty::SET_BY_PROPERTY, $optional, $property->getAttributes()
          )
        );
      }

      return $resolvedProperties;
    } catch (ReflectionException $e) {
      throw new RuntimeException($e->getMessage());
    }
  }
}
