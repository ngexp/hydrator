<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\Pattern;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class PatternTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_match()
  {
    $attr = new Pattern('/^\D+$/');
    $result = $attr->process($this->context("Hello"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_does_not_match()
  {
    $attr = new Pattern('/^\D+$/');
    $result = $attr->process($this->context("33"));

    $this->assertFalse($result->isValid());
  }
}
