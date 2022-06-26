<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Constraints\NegativeOrZero;
use Ngexp\Hydrator\Tests\AttributeTestCase;


class NegativeOrZeroTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_int_value_is_negative()
  {
    $attr = new NegativeOrZero();
    $result = $attr->constraint($this->context(-5));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_is_zero()
  {
    $attr = new NegativeOrZero();
    $result = $attr->constraint($this->context(0));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_is_positive()
  {
    $attr = new NegativeOrZero();
    $result = $attr->constraint($this->context(5));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_float_value_is_negative()
  {
    $attr = new NegativeOrZero();
    $result = $attr->constraint($this->context(-5.0));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_float_value_is_zero()
  {
    $attr = new NegativeOrZero();
    $result = $attr->constraint($this->context(0.0));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_float_value_is_positive()
  {
    $attr = new NegativeOrZero();
    $result = $attr->constraint($this->context(5.0));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_not_a_number()
  {
    $attr = new NegativeOrZero();
    $result = $attr->constraint($this->context("A"));

    $this->assertFalse($result->isValid());
  }
}
