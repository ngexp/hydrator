<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

class ResolvedProperties
{
  /**
   * @var array<\Ngexp\Hydrator\ResolvedProperty>
   */
  private array $properties = [];

  /**
   * Add property to a list of properties.
   *
   * @param string                            $propertyName The name of a field property
   * @param \Ngexp\Hydrator\ResolvedProperty $property
   *
   * @return void
   */
  public function add(string $propertyName, ResolvedProperty $property): void
  {
    $this->properties[$propertyName] = $property;
  }

  /**
   * @return array<string, \Ngexp\Hydrator\ResolvedProperty>
   */
  public function getProperties(): array
  {
    return $this->properties;
  }
}
