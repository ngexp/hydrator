<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Constraints\Max;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class MaxTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_int_value_less_than_max()
  {
    $attr = new Max(10);
    $result = $attr->constraint($this->context(5));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_greater_than_max()
  {
    $attr = new Max(10);
    $result = $attr->constraint($this->context(15));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_int_value_equal_to_max()
  {
    $attr = new Max(10);
    $result = $attr->constraint($this->context(10));

    $this->assertTrue($result->isValid());
  }
}
