<?php

declare(strict_types=1);


namespace Ngexp\Hydrator;

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
   * @param string $errorCode Each message type has a unique identifier.
   *
   * @return array<string, string>
   */
  protected function useTemplate(string $errorCode): array
  {
    return [$errorCode => $this->messageTemplates[$errorCode]];
  }
}
