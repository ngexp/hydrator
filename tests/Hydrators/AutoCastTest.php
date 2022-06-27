<?php

declare(strict_types=1);


namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\AutoCast;
use Ngexp\Hydrator\Tests\AttributeTestCase;
use Ngexp\Hydrator\Type;
use PHPUnit\Framework\TestCase;

class AutoCastTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_auto_cast_to_bool(): void
  {
    $attr = new AutoCast();
    $context = $attr->hydrateValue($this->context("On", Type::BOOL));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::BOOL, $this->getVariableType($result));
    $this->assertTrue($result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_float(): void
  {
    $attr = new AutoCast();
    $context = $attr->hydrateValue($this->context("1.0", Type::FLOAT));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::FLOAT, $this->getVariableType($result));
    $this->assertEquals(1.0, $result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_int(): void
  {
    $attr = new AutoCast();
    $context = $attr->hydrateValue($this->context("1", Type::INT));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::INT, $this->getVariableType($result));
    $this->assertEquals(1, $result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_string(): void
  {
    $attr = new AutoCast();
    $context = $attr->hydrateValue($this->context(1, Type::STRING));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::STRING, $this->getVariableType($result));
    $this->assertEquals("1", $result);
  }

  /** @throws \Exception */
  public function test_auto_cast_to_unsupported_type(): void
  {
    $attr = new AutoCast();
    $context = $attr->hydrateValue($this->context("Hello", Type::ARRAY));

    $this->assertFalse($context->isValid());
  }
}
