<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;
use Countable;
use Iterator;

/**
 * @implements  Iterator<int, \Ngexp\Hydrator\Error>
 */
class ErrorCollection implements Iterator, Countable
{
  private int $position = 0;
  /** @var array<\Ngexp\Hydrator\Error> */
  private array $errors = [];

  public function inheritErrors(ErrorCollection $errors): void
  {
    foreach ($errors as $error) {
      $this->errors[] = $error;
    }
  }

  public function addError(Error $error): void
  {
    $this->errors[] = $error;
  }

  public function first(): Error
  {
    if (count($this->errors) === 0) {
      throw new RuntimeHydrationException("ErrorCollection called when empty");
    }
    return $this->errors[0];
  }

  public function current(): Error
  {
    return $this->errors[$this->position];
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
    return isset($this->errors[$this->position]);
  }

  public function rewind(): void
  {
    $this->position = 0;
  }

  public function count(): int
  {
    return count($this->errors);
  }
}
