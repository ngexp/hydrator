<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Mutators;

use Ngexp\Hydrator\Mutators\ArrayOfClassType;
use Ngexp\Hydrator\Tests\AttributeTestCase;

class History
{
  public int $year;
  public string $event;
}

class ArrayOfClassTypeTest extends AttributeTestCase
{
  /** @throws \Exception */
  public function test_hydrate_array()
  {
    $attr = new ArrayOfClassType(History::class);
    $context = $this->context([["year" => 2022, "event" => "An event"]]);
    $context = $attr->process($context);

    $this->assertTrue($context->isValid());

    /** @var History[] $result */
    $result = $context->getValue();

    $this->assertEquals(2022, $result[0]->year);
    $this->assertEquals("An event", $result[0]->event);
  }
}
