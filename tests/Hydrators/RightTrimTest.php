<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\RightTrim;
use Ngexp\Hydrator\Type;
use PHPUnit\Framework\TestCase;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class RightTrimTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_string_no_right_trim_needed_should_pass()
  {
    $attr = new RightTrim();
    $context = $attr->hydrateValue($this->context("Hello"));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_right_trim_should_pass()
  {
    $attr = new RightTrim();
    $context = $attr->hydrateValue($this->context("  Hello \t  \n  "));

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("  Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_right_trim_of_type_int_should_throw()
  {
    $attr = new RightTrim();
    $context = $attr->hydrateValue($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
