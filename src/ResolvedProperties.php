<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator;

use Countable;
use Iterator;

/**
 * @implements Iterator<\Ngexp\Hydrator\ResolvedProperty>
 */
class ResolvedProperties implements Iterator, Countable
{
  /**
   * @var array<\Ngexp\Hydrator\ResolvedProperty>
   */
  private array $properties = [];
  private int $position = 0;

  public function add(ResolvedProperty $property): void
  {
    $this->properties[] = $property;
  }

  public function current(): ResolvedProperty
  {
    return $this->properties[$this->position];
  }

  public function next(): void
  {
    $this->position += 1;
  }

  public function key(): int
  {
    return $this->position;
  }

  public function valid(): bool
  {
    return isset($this->properties[$this->position]);
  }

  public function rewind(): void
  {
    $this->position = 0;
  }

  public function count(): int
  {
    return count($this->properties);
  }
}
