<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Traits;

use JetBrains\PhpStorm\ArrayShape;
use Ngexp\Hydrator\Constraints\Optional;
use Ngexp\Hydrator\ResolvedProperties;
use Ngexp\Hydrator\ResolvedProperty;
use Ngexp\Hydrator\TypeOf;
use ReflectionClass;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
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
          $this->typeCheck($type);
          $typeOf = $this->getTypeOfType($type);

          $optional = count($method->getAttributes(Optional::class)) === 1;

          $resolvedProperties->add(
            $methodName,
            new ResolvedProperty(
              $methodName, $type, $typeOf, ResolvedProperty::SET_BY_METHOD, $optional, $method->getAttributes()
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

        $type = $property->getType();
        $this->typeCheck($type);
        $typeOf = $this->getTypeOfType($type);

        $resolvedProperties->add(
          $property->getName(),
          new ResolvedProperty(
            $property->getName(), $type, $typeOf, ResolvedProperty::SET_BY_PROPERTY, $optional, $property->getAttributes()
          )
        );
      }

      return $resolvedProperties;
    } catch (ReflectionException $e) {
      throw new RuntimeException($e->getMessage());
    }
  }

  /**
   * @throws \ReflectionException
   */
  private function getTypeOfType(?ReflectionType $reflectionType): TypeOf
  {
    if (!$reflectionType) {
      return TypeOf::NullType;
    }

    if ($reflectionType instanceof ReflectionNamedType) {
       if (class_exists($reflectionType->getName())) {
         $reflectionClass = new ReflectionClass($reflectionType->getName());
         if ($reflectionClass->isEnum()) {
           return TypeOf::EnumType;
         }
         return TypeOf::ClassType;
       }
       return TypeOf::ScalarType;
    } else if ($reflectionType instanceof ReflectionUnionType) {
      return TypeOf::UnionType;
    } else if ($reflectionType instanceof ReflectionIntersectionType) {
      return TypeOf::IntersectionType;
    }

    throw new RuntimeException("Ngexp\\Hydrator internal error");
  }

  /**
   * @throws \ReflectionException
   */
  private function typeCheck(?ReflectionType $reflectionType): void
  {
    if (!$reflectionType) {
      return;
    }

    if ($reflectionType instanceof ReflectionUnionType) {
      foreach ($reflectionType->getTypes() as $type) {
        if (class_exists($type->getName())) {
          // NOTE: This CAN be supported by comparing the possibly multiple classes with the hydration data
          //        but this is too much of a 1% issue to focus on now, or perhaps ever. Time will tell.
          throw new ReflectionException("Ngexp\Hydrator does not support class or enum in a union type");
        }
      }
    } else if ($reflectionType instanceof ReflectionIntersectionType) {
      // NOTE: It's not possible to instantiate an unknown class by its implementation details only.
      throw new ReflectionException("Ngexp\Hydrator does not support intersection types.");
    }
  }
}
