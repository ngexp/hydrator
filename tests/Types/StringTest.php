<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Tests\Types;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Asserts\Optional;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

class StringsToHydrate {
  #[Optional]
  public string $string1;
  #[Optional]
  private string $string2;
  #[Optional]
  public ?string $string3;
  #[Optional]
  private ?string $string4;

  public function getString2(): string
  {
    return $this->string2;
  }

  public function setString2(string $string2): void
  {
    $this->string2 = $string2;
  }

  public function getString4(): ?string
  {
    return $this->string4;
  }

  public function setString4(?string $string4): void
  {
    $this->string4 = $string4;
  }
}

class StringTest extends TestCase
{
  /** @throws \Exception */
  public function test_hydrate_public_string_prop(): void
  {
    $json = '{ "string1": "Hello, world!" }';
    $hydrator = new Hydrator(StringsToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals("Hello, world!", $class->string1);
  }

  /** @throws \Exception */
  public function test_hydrate_public_string_prop_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "string1": null }';
    $hydrator = new Hydrator(StringsToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_string_method(): void
  {
    $json = '{ "string2": "Hello, world!" }';
    $hydrator = new Hydrator(StringsToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals("Hello, world!", $class->getString2());
  }

  /** @throws \Exception */
  public function test_hydrate_public_setter_string_method_with_null_value_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "string2": null }';
    $hydrator = new Hydrator(StringsToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }

  /** @throws \Exception */
  public function test_hydrate_nullable_public_setter_string_method_with_null_value_should_succeed(): void
  {
    $json = '{ "string3": null }';
    $hydrator = new Hydrator(StringsToHydrate::class);

    $class = $hydrator->hydrate(new JsonAdapter($json));

    $this->assertEquals(null, $class->string3);
  }

  /** @throws \Exception */
  public function test_hydrate_setter_string_method_with_int_should_throw(): void
  {
    $this->expectException(HydratorException::class);

    $json = '{ "string3": 33 }';
    $hydrator = new Hydrator(StringsToHydrate::class);

    $hydrator->hydrate(new JsonAdapter($json));
  }
}
