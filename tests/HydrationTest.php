<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests;

use Ngexp\Hydrator\Adapters\ArrayAdapter;
use Ngexp\Hydrator\Hydrator;
use PHPUnit\Framework\TestCase;

class ToInstantiate {
  public int $value;
}

class HydrationTest extends TestCase
{
  /** @throws \Exception */
  public function test_can_instantiate_from_class_name()
  {
    $hydrator = new Hydrator(ToInstantiate::class);
    $instantiated = $hydrator->hydrate(new ArrayAdapter(['value' => 22]));

    $this->assertIsObject($instantiated);
    $this->assertEquals(22, $instantiated->value);
  }

  /** @throws \Exception */
  public function test_can_instantiate_more_than_one_with_the_same_hydration()
  {
    $hydrator = new Hydrator(ToInstantiate::class);
    $instantiated = $hydrator->hydrate(new ArrayAdapter(['value' => 22]));
    $instantiated2 = $hydrator->hydrate(new ArrayAdapter(['value' => 40]));

    $this->assertIsObject($instantiated);
    $this->assertIsObject($instantiated2);
    $this->assertEquals(22, $instantiated->value);
    $this->assertEquals(40, $instantiated2->value);
  }
}
