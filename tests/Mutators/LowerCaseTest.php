<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\LowerCase;
use Ngexp\Hydrator\Tests\AttributeTestCase;


class LowerCaseTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_convert_upper_case_chars_to_lower_case_chars(): void
  {
    $attr = new LowerCase();
    $context = $attr->process($this->context("HELLO"));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("hello", $result);
  }

  /** @throws \Exception */
  public function test_lower_case_invalid_type(): void
  {
    $attr = new LowerCase();
    $context = $attr->process($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
