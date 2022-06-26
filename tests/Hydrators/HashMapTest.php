<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Hydrators;

use Ngexp\Hydrator\Hydrators\HashMap;
use Ngexp\Hydrator\Type;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class HashMapTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_hashmap_from_array()
  {
    $attr = new HashMap("key", "value");
    $context = $this->context([["key" => "hello", "value" => "world"]]);
    $context = $attr->hydrateValue($context);

    $this->assertTrue($context->isValid());

    $result = $context->getValue();

    $this->assertEquals(Type::ARRAY, $this->getVariableType($result));
    $this->assertEquals(["hello" => "world"], $result);
  }

  /** @throws \Exception */
  public function test_hashmap_with_wrong_key_name()
  {
    $attr = new HashMap("key", "value");
    $context = $attr->hydrateValue($this->context([["name" => "hello", "value" => "world"]]));

    $this->assertFalse($context->isValid());
  }

  /** @throws \Exception */
  public function test_hashmap_with_wrong_value_name()
  {
    $attr = new HashMap("key", "value");
    $context = $attr->hydrateValue($this->context([["key" => "hello", "info" => "world"]]));

    $this->assertFalse($context->isValid());
  }

  /** @throws \Exception */
  public function test_not_a_hashmap()
  {
    $attr = new HashMap("key", "value");
    $context = $attr->hydrateValue($this->context([1, 2]));

    $this->assertFalse($context->isValid());
  }
}
