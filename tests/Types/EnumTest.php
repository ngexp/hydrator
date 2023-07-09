<?php

namespace Ngexp\Hydrator\Tests\Types;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Asserts\Optional;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

enum Enums {
  case One;
  case Two;
}

class EnumToHydrate {
  #[Optional]
  public Enums $value;

  #[Optional]
  private Enums $value2;

  #[Optional]
  public ?Enums $value3;

  public function setValue2(Enums $value): void
  {
    $this->value2 = $value;
  }

  public function getValue2(): Enums
  {
    return $this->value2;
  }
}

class EnumTest extends TestCase
{
  /** @throws \Exception */
  public function test_hydrate_public_enum_prop(): void
  {
    $json = '{ "value": "One" }';
    $hydrator = new Hydrator(EnumToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(Enums::One, $class->value);
  }

  /** @throws \Exception */
  public function test_hydrate_public_enum_prop_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "value": null }';
    $hydrator = new Hydrator(EnumToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_enum_method(): void
  {
    $json = '{ "value2": "One" }';
    $hydrator = new Hydrator(EnumToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(Enums::One, $class->getValue2());
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_enum_method_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "value2": null }';
    $hydrator = new Hydrator(EnumToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_nullable_public_setter_enum_method_with_null_value_should_succeed(): void
  {
    $json = '{ "value3": null }';
    $hydrator = new Hydrator(EnumToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(null, $class->value3);
  }

  /** @throws \Exception */
  public function test_hydrate_setter_enum_method_with_non_supporting_case_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "value3": "Three" }';
    $hydrator = new Hydrator(EnumToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }
}
