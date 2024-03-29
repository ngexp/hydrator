<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\AutoCast;
use Ngexp\Hydrator\Tests\AttributeTestCase;
use Ngexp\Hydrator\Type;

class AutoCastTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_auto_cast_to_bool(): void
  {
    $attr = new AutoCast();
    $context = $attr->process($this->context("On", Type::BOOL));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertTrue($result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_float(): void
  {
    $attr = new AutoCast();
    $context = $attr->process($this->context("1.0", Type::FLOAT));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::FLOAT, $this->getVariableType($result));
    $this->assertEquals(1.0, $result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_int(): void
  {
    $attr = new AutoCast();
    $context = $attr->process($this->context("1", Type::INT));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::INT, $this->getVariableType($result));
    $this->assertEquals(1, $result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_string(): void
  {
    $attr = new AutoCast();
    $context = $attr->process($this->context(1, Type::STRING));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::STRING, $this->getVariableType($result));
    $this->assertEquals("1", $result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_unsupported_type(): void
  {
    $attr = new AutoCast();
    $context = $attr->process($this->context("Hello", Type::ARRAY));

    $this->assertFalse($context->isValid());
  }
}
