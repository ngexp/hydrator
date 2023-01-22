<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

use Ngexp\Hydrator\Traits\StringFormatting;

class FailureMessage
{
  use StringFormatting;

  private readonly string $message;

  /**
   * @param array<string, string> $parameters
   */
  private function __construct(
    private readonly string $errorCode,
    array                   $parameters,
    string                  $message
  ) {
    $this->message = $this->hydrateString($parameters, $message);
  }

  /**
   * @param array<string, string> $message
   * @param array<string, string> $parameters
   */
  static public function create(Context $context, array $message, array $parameters = []): FailureMessage
  {
    $parameters = array_merge(
      [
        "propertyName" => $context->hasProperty() ? $context->getProperty()->getPropertyName() : "",
        "expectedType" => $context->hasProperty() ? $context->getExpectedType() : "",
        "value" => $context->getValue(),
        "valueType" => $context->getValueType(),
      ],
      $parameters
    );

    $errorCode = key($message) ?? "ERROR";

    return new FailureMessage($errorCode, $parameters, $message[$errorCode]);
  }

  public function getErrorCode(): string
  {
    return $this->errorCode;
  }

  public function getMessage(): string
  {
    return $this->message;
  }
}
