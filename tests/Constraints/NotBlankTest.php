<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Constraints;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\NotBlank;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class NotBlankTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_regular_string()
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context("Hello, world!"));

    $this->assertTrue($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_is_empty()
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context(""));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_spaces_only()
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context("  "));

    $this->assertFalse($result->isValid());
  }

  /** @throws \Exception */
  public function test_string_contains_white_spaces()
  {
    $attr = new NotBlank();
    $result = $attr->process($this->context("\t\n"));

    $this->assertFalse($result->isValid());
  }
}
