<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

class Error
{
  const INTERNAL_CUSTOM_MESSAGE = "30bee7b4-c32a-4225-9422-b3722311fb9a";

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
