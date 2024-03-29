<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Min;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class MinTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_int_value_greater_than_min(): void
  {
    $attr = new Min(5);
    $result = $attr->process($this->context(10));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_less_than_min(): void
  {
    $attr = new Min(5);
    $result = $attr->process($this->context(1));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_equal_to_min(): void
  {
    $attr = new Min(5);
    $result = $attr->process($this->context(5));

    $this->assertTrue($result->isValid());
  }
}
