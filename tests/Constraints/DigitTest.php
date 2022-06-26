<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Constraints\Digit;
use PHPUnit\Framework\TestCase;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class DigitTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_digit()
  {
    $attr = new Digit();
    $result = $attr->constraint($this->context("123"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_digit()
  {
    $attr = new Digit();
    $result = $attr->constraint($this->context("not a number"));

    $this->assertFalse($result->isValid());
  }
}
