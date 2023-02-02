<?php

declare(strict_types=1);


namespace Ngexp\Hydrator;

use ArrayIterator;
use Ngexp\Hydrator\Traits\StringFormatting;
use RuntimeException;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, string>
 */
class ErrorMessageAggregate implements IteratorAggregate
{
  use StringFormatting;

  /** @var array<string, string> */
  private readonly array $errorMessages;

  /**
   * @param \Ngexp\Hydrator\ErrorCollection $errors
   * @param array<string, string>           $customErrorMessages
   */
  public function __construct(private readonly ErrorCollection $errors, array $customErrorMessages = [])
  {
    $defaultErrorMessages = require "ErrorMessages.php";
    $this->errorMessages = array_merge($defaultErrorMessages, $customErrorMessages);
  }

  public function first(): string
  {
    if (count($this->errorMessages) === 0) {
      throw new RuntimeException("Array of messages is empty");
    }
    $error = $this->errors->first();

    return $this->getMessage($error);
  }

  public function getIterator(): Traversable
  {
    $messages = [];
    foreach ($this->errors as $error) {
      $messages[] = $this->getMessage($error);
    }
    return new ArrayIterator($messages);
  }

  /**
   * Return current error message
   *
   * @param \Ngexp\Hydrator\Error $error
   *
   * @return string
   */
  public function getMessage(Error $error): string
  {
    $context = $error->getContext();
    /** @var array<string, mixed> $parameters */
    $parameters = array_merge(
      $error->getParameters(),
      [
        "className" => $context->getClassType()->getClassName(),
        "propertyName" => $context->hasProperty() ? $context->getProperty()->getPropertyName() : "",
        "expectedType" => $context->hasProperty() ? $context->getExpectedType() : "",
        "value" => $context->getValue(),
        "valueType" => $context->getValueType(),
      ],
    );

    // Hack to be able to set arbitrary custom messages.
    if ($error->getCode() === Error::INTERNAL_CUSTOM_MESSAGE) {
      $message = $error->getParameters()["internalMessage"];
      if (!is_string($message)) {
        throw new RuntimeException("Ngexp\\Hydrator: Internal message not a string");
      }
    } else {
      $message = $this->findErrorMessage($error->getCode(), $this->errorMessages);
    }

    // If we have a parentContext, wrap the current message into the ErrorCode::CHILD_OF_PARENT error message.
    $parentContext = $context->getParentContext();
    while ($parentContext) {
      $parameters = array_merge(
        $parameters,
        [
          "parentClassName" => $parentContext->getClassType()->getClassName(),
          "message" => $message,
        ]
      );
      $message = $this->findErrorMessage(ErrorCode::CHILD_OF_PARENT, $this->errorMessages);
      $message = $this->hydrateString($message, $parameters);
      $parentContext = $parentContext->getParentContext();
    }

    return $this->hydrateString($message, $parameters);
  }

  /**
   * @param string                $code
   * @param array<string, string> $errorMessages
   *
   * @return string
   */
  private function findErrorMessage(string $code, array $errorMessages): string
  {
    if (!array_key_exists($code, $errorMessages)) {
      throw new RuntimeException("Can not find error message for code: $code");
    }
    return $errorMessages[$code];
  }
}
