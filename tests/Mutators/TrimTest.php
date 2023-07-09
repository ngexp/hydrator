<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\Trim;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class TrimTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_no_trimming_needed_should_pass(): void
  {
    $attr = new Trim();
    $context = $attr->process($this->context("Hello"));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_trimming_should_pass(): void
  {
    $attr = new Trim();
    $context = $attr->process($this->context("  Hello "));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_trimming_of_type_int_should_throw(): void
  {
    $attr = new Trim();
    $context = $attr->process($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
