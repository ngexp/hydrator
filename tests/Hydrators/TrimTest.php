<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\Trim;
use Ngexp\Hydrator\Type;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class TrimTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_no_trimming_needed_should_pass()
  {
    $attr = new Trim();
    $context = $attr->hydrateValue($this->context("Hello"));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_trimming_should_pass()
  {
    $attr = new Trim();
    $context = $attr->hydrateValue($this->context("  Hello "));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_trimming_of_type_int_should_throw()
  {
    $attr = new Trim();
    $context = $attr->hydrateValue($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
