<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Alnum;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class AlnumTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_valid_alnum(): void
  {
    $attr = new Alnum();
    $result = $attr->process($this->context("ABC123"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_invalid_alnum(): void
  {
    $attr = new Alnum();
    $result = $attr->process($this->context("*"));

    $this->assertFalse($result->isValid());
  }
}
