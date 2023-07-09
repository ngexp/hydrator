<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\CoerceString;
use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\Type;
use stdClass;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class CoerceStringTest extends AttributeTestCase
{
  use ReflectionUtils;

  /** @throws \Exception */
  public function test_coerce_string_from_string()
  {
    $attr = new CoerceString();
    $context = $this->context("Hello, world!");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::STRING, $this->getVariableType($result));
    $this->assertEquals("Hello, world!", $result);
  }

  /** @throws \Exception */
  public function test_coerce_string_from_int()
  {
    $attr = new CoerceString();
    $context = $this->context(100);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::STRING, $this->getVariableType($result));
    $this->assertEquals("100", $result);
  }

  /** @throws \Exception */
  public function test_coerce_string_from_float_should_pass()
  {
    $attr = new CoerceString();
    $context = $this->context(100.1);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::STRING, $this->getVariableType($result));
    $this->assertEquals("100.1", $result);
  }

  /** @throws \Exception */
  public function test_coerce_string_from_object_should_throw()
  {
    $attr = new CoerceString();
    $context = $attr->process($this->context(new stdClass()));

    $this->assertFalse($context->isValid());
  }

  /** @throws \Exception */
  public function test_coerce_string_from_array_should_throw()
  {
    $attr = new CoerceString();
    $context = $attr->process($this->context([]));

    $this->assertFalse($context->isValid());
  }
}
