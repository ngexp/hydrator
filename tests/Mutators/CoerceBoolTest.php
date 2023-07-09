<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\CoerceBool;
use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\Type;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class CoerceBoolTest extends AttributeTestCase
{
  use ReflectionUtils;

  /** @throws \Exception */
  public function test_coerce_bool_from_int_value_1(): void
  {
    $attr = new CoerceBool();
    $context = $this->context(1);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertTrue($result);
  }

  /** @throws \Exception */
  public function test_coerce_bool_from_int_value_0(): void
  {
    $attr = new CoerceBool();
    $context = $this->context(0);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertFalse($result);
  }

  /** @throws \Exception */
  public function test_coerce_bool_from_string_value_1(): void
  {
    $attr = new CoerceBool();
    $context = $this->context("1");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertTrue($result);
  }

  /** @throws \Exception */
  public function test_coerce_bool_from_string_value_0(): void
  {
    $attr = new CoerceBool();
    $context = $this->context("0");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertFalse($result);
  }

  /** @throws \Exception */
  public function test_coerce_bool_from_string_true(): void
  {
    $attr = new CoerceBool();
    $context = $this->context("true");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertTrue($result);
  }

  /** @throws \Exception */
  public function test_coerce_bool_from_string_false(): void
  {
    $attr = new CoerceBool();
    $context = $this->context("false");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertFalse($result);
  }

  /** @throws \Exception */
  public function test_coerce_bool_from_non_bool_string_value(): void
  {
    $attr = new CoerceBool();
    $context = $this->context("hello");
    $context = $attr->process($context);

    $this->assertFalse($context->isValid());
  }
}
