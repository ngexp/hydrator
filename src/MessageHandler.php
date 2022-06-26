<?php

declare(strict_types=1);


namespace Ngexp\Hydrator;

use JetBrains\PhpStorm\ArrayShape;

abstract class MessageHandler
{
  /**
   * Messages uses for failures, this can be overridden from the constructor.
   *
   * @var array<string, string>
   */
  protected array $messageTemplates = [];

  /**
   * Override the default message templates if the supplied array size is greater than 0
   *
   * @param array<string, string> $messageTemplates
   */
  protected function updateMessageTemplates(array $messageTemplates): void
  {
    if (count($messageTemplates) > 0) {
      $this->messageTemplates = array_merge($this->messageTemplates, $messageTemplates);
    }
  }

  /**
   * Return the messageTemplate that match the supplied key
   *
   * @param string $code Each message type has a unique identifier.
   *
   * @return array<string, string>
   */
  protected function template(string $code): array
  {
    return [$code => $this->messageTemplates[$code]];
  }
}
