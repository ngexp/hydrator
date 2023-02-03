<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Constraints\Alnum;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class AlnumTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_alnum()
  {
    $attr = new Alnum();
    $result = $attr->process($this->context("ABC123"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_alnum()
  {
    $attr = new Alnum();
    $result = $attr->process($this->context("*"));

    $this->assertFalse($result->isValid());
  }
}
