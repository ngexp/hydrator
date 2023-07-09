<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\RightTrim;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class RightTrimTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_no_right_trim_needed_should_pass(): void
  {
    $attr = new RightTrim();
    $context = $attr->process($this->context("Hello"));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_right_trim_should_pass(): void
  {
    $attr = new RightTrim();
    $context = $attr->process($this->context("  Hello \t  \n  "));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("  Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_right_trim_of_type_int_should_throw(): void
  {
    $attr = new RightTrim();
    $context = $attr->process($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
