<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\CoerceInt;
use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\Type;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class CoerceIntTest extends AttributeTestCase
{
  use ReflectionUtils;

  /** @throws \Exception */
  public function test_coerce_int_from_int()
  {
    $attr = new CoerceInt();
    $context = $this->context(1);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::INT, $this->getVariableType($result));
    $this->assertEquals(1, $result);
  }

  /** @throws \Exception */
  public function test_coerce_int_from_string()
  {
    $attr = new CoerceInt();
    $context = $this->context("Hello");
    $context = $attr->process($context);

    $this->assertFalse($context->isValid());
  }

  /** @throws \Exception */
  public function test_coerce_int_from_float()
  {
    $attr = new CoerceInt();
    $context = $this->context(1.0);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::INT, $this->getVariableType($result));
    $this->assertEquals(1, $result);
  }

  /** @throws \Exception */
  public function test_coerce_int_from_string_number()
  {
    $attr = new CoerceInt();
    $context = $this->context("100");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::INT, $this->getVariableType($result));
    $this->assertEquals(100, $result);
  }
}
