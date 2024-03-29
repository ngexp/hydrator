<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\CoerceFloat;
use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\Type;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class CoerceFloatTest extends AttributeTestCase
{
  use ReflectionUtils;

  /** @throws \Exception */
  public function test_coerce_float_from_float_value(): void
  {
    $attr = new CoerceFloat();
    $context = $this->context(1.0);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::FLOAT, $this->getVariableType($result));
    $this->assertEquals(1.0, $result);
  }

  /** @throws \Exception */
  public function test_coerce_float_from_string_char(): void
  {
    $attr = new CoerceFloat();
    $context = $this->context("Hello");
    $context = $attr->process($context);
    $this->assertFalse($context->isValid());
  }

  /** @throws \Exception */
  public function test_coerce_float_from_int(): void
  {
    $attr = new CoerceFloat();
    $context = $this->context(1);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::FLOAT, $this->getVariableType($result));
    $this->assertEquals(1.0, $result);
  }

  /** @throws \Exception */
  public function test_coerce_float_from_string_number(): void
  {
    $attr = new CoerceFloat();
    $context = $this->context("100");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::FLOAT, $this->getVariableType($result));
    $this->assertEquals(100, $result);
  }
}
