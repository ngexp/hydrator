<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\JsonDecode;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class ClassToHydrate {
  public string $hello;
}

class JsonDecodeTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_json_decode_should_pass()
  {
    $attr = new JsonDecode();
    $context = $this->context("{ \"hello\": \"world\" }", expectedType: ClassToHydrate::class);
    $context = $attr->hydrateValue($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(ClassToHydrate::class, get_class($result));
    $this->assertEquals("world", $result->hello);
  }

  /** @throws \Exception */
  public function test_json_decode_with_faulty_data()
  {
    $attr = new JsonDecode();
    $context = $attr->hydrateValue($this->context("xx", expectedType: ClassToHydrate::class));

    $this->assertFalse($context->isValid());
  }
}
