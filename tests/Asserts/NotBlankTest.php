<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Asserts;

use Ngexp\Hydrator\Asserts\NotBlank;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class NotBlankTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_regular_string(): void
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context("Hello, world!"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_is_empty(): void
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context(""));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_spaces_only(): void
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context("  "));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_white_spaces(): void
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context("\t\n"));

    $this->assertFalse($result->isValid());
  }
}
