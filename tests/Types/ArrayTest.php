<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Types;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\Optional;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

class ArraysToHydrate {
  #[Optional]
  public array $array1;
  #[Optional]
  private array $array2;
  #[Optional]
  public ?array $array3;
  #[Optional]
  private ?array $array4;

  public function setArray2(array $array2): void
  {
    $this->array2 = $array2;
  }

  public function getArray2():array
  {
    return $this->array2;
  }

  public function setArray4(?array $array4): void
  {
    $this->array4 = $array4;
  }

  public function getArray4(): ?array
  {
    return $this->array4;
  }
}

class ArrayTest extends TestCase
{
  /** @throws \Exception */
  public function test_hydrate_public_array_prop(): void
  {
    $json = '{ "array1": [1, 2, 3] }';
    $hydrator = new Hydrator(ArraysToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals([1, 2, 3], $class->array1);
  }

  /** @throws \Exception */
  public function test_hydrate_public_array_prop_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "array1": null }';
    $hydrator = new Hydrator(ArraysToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_array_method(): void
  {
    $json = '{ "array2": [1, 2, 3] }';
    $hydrator = new Hydrator(ArraysToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals([1, 2, 3], $class->getArray2());
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_array_method_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "array2": null }';
    $hydrator = new Hydrator(ArraysToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_nullable_public_setter_array_method_with_null_value_should_succeed(): void
  {
    $json = '{ "array3": null }';
    $hydrator = new Hydrator(ArraysToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(null, $class->array3);
  }

  /** @throws \Exception */
  public function test_hydrate_setter_array_method_with_string_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "array3": "[1, 2, 3]" }';
    $hydrator = new Hydrator(ArraysToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }
}
