<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Constraints\Alpha;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class AlphaTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_alpha()
  {
    $attr = new Alpha();
    $result = $attr->constraint($this->context("ABC"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_alpha()
  {
    $attr = new Alpha();
    $result = $attr->constraint($this->context("ABC123"));

    $this->assertFalse($result->isValid());
  }
}
