<?php

declare(strict_types=1);


namespace Ngexp\Hydrator\Tests;

use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ResolvedProperty;
use Ngexp\Hydrator\Traits\ReflectionUtils;
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
    $property = new ResolvedProperty("", $reflectionNamedTypeStub, ResolvedProperty::SET_BY_PROPERTY, false, []);
    return new Context($property, $value);
  }
}
