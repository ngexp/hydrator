<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\HashMap;
use Ngexp\Hydrator\Type;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class HashMapTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_hashmap_from_array(): void
  {
    $attr = new HashMap("key", "value");
    $context = $this->context([["key" => "hello", "value" => "world"]]);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::ARRAY, $this->getVariableType($result));
    $this->assertEquals(["hello" => "world"], $result);
  }

  /** @throws \Exception */
  public function test_hashmap_with_wrong_key_name(): void
  {
    $attr = new HashMap("key", "value");
    $context = $attr->process($this->context([["name" => "hello", "value" => "world"]]));

    $this->assertFalse($context->isValid());
  }

  /** @throws \Exception */
  public function test_hashmap_with_wrong_value_name(): void
  {
    $attr = new HashMap("key", "value");
    $context = $attr->process($this->context([["key" => "hello", "info" => "world"]]));

    $this->assertFalse($context->isValid());
  }

  /** @throws \Exception */
  public function test_not_a_hashmap(): void
  {
    $attr = new HashMap("key", "value");
    $context = $attr->process($this->context([1, 2]));

    $this->assertFalse($context->isValid());
  }
}
