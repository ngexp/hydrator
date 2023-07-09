<?php

declare(strict_types=1);


namespace Ngexp\Hydrator\Tests;

use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Mutators\ClassType;
use Ngexp\Hydrator\ResolvedProperty;
use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\TypeOf;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;

abstract class AttributeTestCase extends TestCase
{
  use ReflectionUtils;

  public function context(mixed $value, string $expectedType = ""): Context
  {
    $reflectionNamedTypeStub = $this->createStub(ReflectionNamedType::class);
    $reflectionNamedTypeStub
      ->method("getName")
      ->willReturn($expectedType !== "" ? $expectedType : $this->getVariableType($value));
    $property = new ResolvedProperty(
      "", $reflectionNamedTypeStub, TypeOf::NullType, ResolvedProperty::SET_BY_PROPERTY, false, []
    );
    $classTypeStub = $this->createStub(ClassType::class);
    return new Context($property, $value, $classTypeStub);
  }
}
