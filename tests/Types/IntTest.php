<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Types;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\Optional;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

class IntegersToHydrate {
  #[Optional]
  public int $int1;
  #[Optional]
  private int $int2;
  #[Optional]
  public ?int $int3;
  #[Optional]
  private ?int $int4;

  public function setInt2(int $int2): void
  {
    $this->int2 = $int2;
  }

  public function getInt2(): int
  {
    return $this->int2;
  }

  public function setInt4(?int $int4): void
  {
    $this->int4 = $int4;
  }

  public function getInt4(): ?int
  {
    return $this->int4;
  }
}

class IntTest extends TestCase
{
  /** @throws \Exception */
  public function test_hydrate_public_int_prop(): void
  {
    $json = '{ "int1": 99 }';
    $hydrator = new Hydrator(IntegersToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(99, $class->int1);
  }

  /** @throws \Exception */
  public function test_hydrate_public_int_prop_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "int1": null }';
    $hydrator = new Hydrator(IntegersToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_int_method(): void
  {
    $json = '{ "int2": 33 }';
    $hydrator = new Hydrator(IntegersToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(33, $class->getInt2());
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_int_method_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "int2": null }';
    $hydrator = new Hydrator(IntegersToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_nullable_public_setter_int_method_with_null_value_should_succeed(): void
  {
    $json = '{ "int3": null }';
    $hydrator = new Hydrator(IntegersToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(null, $class->int3);
  }

  /** @throws \Exception */
  public function test_hydrate_setter_int_method_with_string_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "int3": "33" }';
    $hydrator = new Hydrator(IntegersToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }
}
