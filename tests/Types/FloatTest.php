<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Types;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Asserts\Optional;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

class FloatToHydrate {
  #[Optional]
  public float $float1;
  #[Optional]
  private float $float2;
  #[Optional]
  public ?float $float3;
  #[Optional]
  private ?float $float4;

  public function setFloat2(float $float2): void
  {
    $this->float2 = $float2;
  }

  public function getFloat2(): float
  {
    return $this->float2;
  }

  public function setFloat4(?float $float4): void
  {
    $this->float4 = $float4;
  }

  public function getFloat4(): ?float
  {
    return $this->float4;
  }
}

class FloatTest extends TestCase
{
  /** @throws \Exception */
  public function test_hydrate_public_float_prop(): void
  {
    $json = '{ "float1": 99.0 }';
    $hydrator = new Hydrator(FloatToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(99, $class->float1);
  }

  /** @throws \Exception */
  public function test_hydrate_public_float_prop_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "float1": null }';
    $hydrator = new Hydrator(FloatToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_float_method(): void
  {
    $json = '{ "float2": 33.0 }';
    $hydrator = new Hydrator(FloatToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(33, $class->getFloat2());
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_float_method_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "float2": null }';
    $hydrator = new Hydrator(FloatToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_nullable_public_setter_float_method_with_null_value_should_succeed(): void
  {
    $json = '{ "float3": null }';
    $hydrator = new Hydrator(FloatToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(null, $class->float3);
  }

  /** @throws \Exception */
  public function test_hydrate_setter_float_method_with_string_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "float3": "33" }';
    $hydrator = new Hydrator(FloatToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }
}
