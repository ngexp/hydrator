<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\LeftTrim;
use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class LeftTrimTest extends AttributeTestCase
{
  use ReflectionUtils;

  /** @throws \Exception */
  public function test_string_no_left_trim_needed_should_pass(): void
  {
    $attr = new LeftTrim();
    $context = $this->context("Hello");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello", $result);
  }

  /** @throws \Exception */
  public function test_string_left_trim_should_pass(): void
  {
    $attr = new LeftTrim();
    $context = $this->context("\t  \n Hello  ");
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals("Hello  ", $result);
  }

  /** @throws \Exception */
  public function test_string_left_trim_of_type_int(): void
  {
    $attr = new LeftTrim();
    $context = $attr->process($this->context(66));

    $this->assertFalse($context->isValid());
  }
}
