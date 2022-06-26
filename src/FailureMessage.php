<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

class FailureMessage
{
  public function __construct(private string $code, private string $message)
  {
  }

  public function getCode(): string
  {
    return $this->code;
  }

  public function getMessage(): string
  {
    return $this->message;
  }
}
