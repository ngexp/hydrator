<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\UpperCase;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class UpperCaseTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_convert_lower_case_to_upper_case_chars()
  {
    $attr = new UpperCase();
    $context = $attr->process($this->context("hello"));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("HELLO", $result);
  }

  /** @throws \Exception */
  public function test_upper_case_invalid_type()
  {
    $attr = new UpperCase();
    $context = $attr->process($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
