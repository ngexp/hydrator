<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\JsonDecode;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class ClassToHydrate {
  public string $hello;
}

class JsonDecodeTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_json_decode_should_pass(): void
  {
    $attr = new JsonDecode();
    $context = $this->context("{ \"hello\": \"world\" }", expectedType: ClassToHydrate::class);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    /** @var ClassToHydrate $result */
    $result = $context->getValue();

    $this->assertEquals(ClassToHydrate::class, get_class($result));
    $this->assertEquals("world", $result->hello);
  }

  /** @throws \Exception */
  public function test_json_decode_with_faulty_data(): void
  {
    $attr = new JsonDecode();
    $context = $attr->process($this->context("xx", expectedType: ClassToHydrate::class));

    $this->assertFalse($context->isValid());
  }
}
