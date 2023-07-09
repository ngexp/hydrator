<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Digit;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class DigitTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_digit(): void
  {
    $attr = new Digit();
    $result = $attr->process($this->context("123"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_digit(): void
  {
    $attr = new Digit();
    $result = $attr->process($this->context("not a number"));

    $this->assertFalse($result->isValid());
  }
}
