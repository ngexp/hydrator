<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Positive;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class PositiveTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_int_value_is_positive(): void
  {
    $attr = new Positive();
    $result = $attr->process($this->context(5));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_is_zero(): void
  {
    $attr = new Positive();
    $result = $attr->process($this->context(0));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_is_negative(): void
  {
    $attr = new Positive();
    $result = $attr->process($this->context(-5));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_float_value_is_positive(): void
  {
    $attr = new Positive();
    $result = $attr->process($this->context(5.0));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_float_value_is_zero_should_throw(): void
  {
    $attr = new Positive();
    $result = $attr->process($this->context(0.0));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_float_value_is_negative_should_throw(): void
  {
    $attr = new Positive();
    $result = $attr->process($this->context(-5.0));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_not_a_number(): void
  {
    $attr = new Positive();
    $result = $attr->process($this->context("A"));

    $this->assertFalse($result->isValid());
  }
}
