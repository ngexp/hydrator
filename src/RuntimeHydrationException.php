<?php

declare(strict_types=1);


namespace Ngexp\Hydrator;

use RuntimeException;

class RuntimeHydrationException extends RuntimeException
{
  public function __construct(string $message)
  {
    $message = "Ngexp\\Hydrator: $message";
    parent::__construct($message);
  }
}
