<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

class Error
{
  // Hopefully unique enough :^)
  const INTERNAL_CUSTOM_MESSAGE = "94729274498208";

  /**
   * @param \Ngexp\Hydrator\Context $context
   * @param string                  $code
   * @param array<string, mixed>   $parameters
   */
  public function __construct(
    private readonly Context $context,
    private readonly string  $code,
    private readonly array   $parameters,
  )
  {
  }

  public function getCode(): string
  {
    return $this->code;
  }

  public function getContext(): Context
  {
    return $this->context;
  }

  /**
   * @return array<string, mixed>
   */
  public function getParameters(): array
  {
    return $this->parameters;
  }
}
