<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Types;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Asserts\Optional;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

class BoolToHydrate {
  #[Optional]
  public bool $bool1;
  #[Optional]
  private bool $bool2;
  #[Optional]
  public ?bool $bool3;
  #[Optional]
  private ?bool $bool4;

  public function setBool2(bool $bool2): void
  {
    $this->bool2 = $bool2;
  }

  public function getBool2(): bool
  {
    return $this->bool2;
  }

  public function setBool4(?bool $bool4): void
  {
    $this->bool4 = $bool4;
  }

  public function getBool4(): ?bool
  {
    return $this->bool4;
  }
}

class BoolTest extends TestCase
{
  /** @throws \Exception */
  public function test_hydrate_public_bool_prop(): void
  {
    $json = '{ "bool1": true }';
    $hydrator = new Hydrator(BoolToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(true, $class->bool1);
  }

  /** @throws \Exception */
  public function test_hydrate_public_bool_prop_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "bool1": null }';
    $hydrator = new Hydrator(BoolToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_bool_method(): void
  {
    $json = '{ "bool2": false }';
    $hydrator = new Hydrator(BoolToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(false, $class->getBool2());
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_bool_method_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "bool2": null }';
    $hydrator = new Hydrator(BoolToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_nullable_public_setter_bool_method_with_null_value_should_succeed(): void
  {
    $json = '{ "bool3": null }';
    $hydrator = new Hydrator(BoolToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(null, $class->bool3);
  }

  /** @throws \Exception */
  public function test_hydrate_setter_bool_method_with_string_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "bool3": "true" }';
    $hydrator = new Hydrator(BoolToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_setter_bool_method_with_bool_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "bool3": 1 }';
    $hydrator = new Hydrator(BoolToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }
}
