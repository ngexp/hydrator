<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\LowerCase;
use Ngexp\Hydrator\Tests\AttributeTestCase;


class LowerCaseTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_convert_upper_case_chars_to_lower_case_chars()
  {
    $attr = new LowerCase();
    $context = $attr->hydrateValue($this->context("HELLO"));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("hello", $result);
  }

  /** @throws \Exception */
  public function test_lower_case_invalid_type()
  {
    $attr = new LowerCase();
    $context = $attr->hydrateValue($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
