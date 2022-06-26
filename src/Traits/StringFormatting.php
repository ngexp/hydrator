<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Traits;

trait StringFormatting
{
  public function valueToStringRepresentation(mixed $value): string
  {
    if (is_null($value)) return 'null';
    if (is_object($value)) return '(object)';
    if (is_array($value)) return '(array)';
    if (is_string($value)) return $value;

    return strval($value);
  }

  /**
   * @param array<string, mixed> $parameters Data used by {placeholder} text.
   * @param string               $message    The message to hydrate.
   *
   * @return string
   */
  public function hydrateString(array $parameters, string $message): string
  {
    foreach ($parameters as $key => $data) {
      $message = str_replace("{" . $key . "}", $this->valueToStringRepresentation($data), $message);
    }
    return $message;
  }
}
