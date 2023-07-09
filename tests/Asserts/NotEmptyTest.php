<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\NotEmpty;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class NotEmptyTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_is_not_empty(): void
  {
    $attr = new NotEmpty();
    $result = $attr->process($this->context("Hello, world!"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_spaces(): void
  {
    $attr = new NotEmpty();
    $result = $attr->process($this->context("  "));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_white_spaces(): void
  {
    $attr = new NotEmpty();
    $result = $attr->process($this->context("\t\n"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_is_empty(): void
  {
    $attr = new NotEmpty();
    $result = $attr->process($this->context(""));

    $this->assertFalse($result->isValid());
  }
}
