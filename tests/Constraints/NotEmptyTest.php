<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Constraints\NotEmpty;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class NotEmptyTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_is_not_empty()
  {
    $attr = new NotEmpty();
    $result = $attr->constraint($this->context("Hello, world!"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_spaces()
  {
    $attr = new NotEmpty();
    $result = $attr->constraint($this->context("  "));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_white_spaces()
  {
    $attr = new NotEmpty();
    $result = $attr->constraint($this->context("\t\n"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_is_empty()
  {
    $attr = new NotEmpty();
    $result = $attr->constraint($this->context(""));

    $this->assertFalse($result->isValid());
  }
}
