<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Alpha;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class AlphaTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_alpha()
  {
    $attr = new Alpha();
    $result = $attr->process($this->context("ABC"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_alpha()
  {
    $attr = new Alpha();
    $result = $attr->process($this->context("ABC123"));

    $this->assertFalse($result->isValid());
  }
}
