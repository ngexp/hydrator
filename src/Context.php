<?php

declare(strict_types=1);


namespace Ngexp\Hydrator;

use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\Traits\StringFormatting;
use RuntimeException;

class Context
{
  use ReflectionUtils;
  use StringFormatting;

  /**
   * @var array<\Ngexp\Hydrator\FailureMessage> array that stores all the message failures generated.
   */
  private array $failureMessages = [];
  private bool $isValid = true;

  /**
   * @param \Ngexp\Hydrator\ResolvedProperty|null $property
   * @param mixed                                  $value
   */
  public function __construct(private ?ResolvedProperty $property, private mixed $value)
  {
  }

  public function hasProperty(): bool
  {
    return !is_null($this->property);
  }

  public function getProperty(): ResolvedProperty
  {
    assert(!is_null($this->property));
    return $this->property;
  }

  public function getValue(): mixed
  {
    return $this->value;
  }

  public function getExpectedType(): string
  {
    assert(!is_null($this->property));
    return $this->property->getType();
  }

  public function isClassProperty(): bool
  {
    return !is_null($this->property) && class_exists($this->property->getType());
  }

  /**
   * @return class-string
   */
  public function getClassName(): string
  {
    assert(!is_null($this->property));

    $className = $this->property->getType();
    if (class_exists($className)) {
      return $className;
    }
    throw new RuntimeException("Trying to get class name from a non class type");
  }

  public function getValueType(): string
  {
    return $this->getVariableType($this->value);
  }

  public function setValue(mixed $value): void
  {
    $this->value = $value;
  }

  /**
   * @return array<\Ngexp\Hydrator\FailureMessage>
   */
  public function getFailureMessages(): array
  {
    return $this->failureMessages;
  }

  public function inheritFailState(Context $context): void
  {
    foreach ($context->getFailureMessages() as $failureMessage) {
      $this->failureMessages[] = $failureMessage;
    }
    $this->isValid = $context->isValid();
  }

  public function withValue(mixed $value): Context
  {
    $this->value = $value;
    return $this;
  }

  /**
   * @param array<string, string> $message
   * @param array<string, mixed>  $extraParameters
   *
   * @return Context
   */
  public function withMainFailure(array $message, array $extraParameters = []): Context
  {
    array_unshift($this->failureMessages, $this->createFailureMessage($message, $extraParameters));
    $this->isValid = false;
    return $this;
  }

  /**
   * @param array<string, string> $message
   * @param array<string, mixed>  $extraParameters
   *
   * @return Context
   */
  public function withFailure(array $message, array $extraParameters = []): Context
  {
    $this->failureMessages[] = $this->createFailureMessage($message, $extraParameters);
    $this->isValid = false;

    return $this;
  }

  /**
   * @param array<string, string> $message         Failure message with its code and message text.
   * @param array<string, mixed>  $extraParameters Contains extra placeholder values for specific failure messages.
   *
   * @return \Ngexp\Hydrator\FailureMessage
   */
  private function createFailureMessage(array $message, array $extraParameters = []): FailureMessage
  {
    $parameters = array_merge(
      [
        "propertyName" => $this->hasProperty() ? $this->getProperty()->getPropertyName() : "",
        "expectedType" => $this->hasProperty() ? $this->getExpectedType() : "",
        "value" => $this->value,
        "valueType" => $this->getValueType(),
      ],
      $extraParameters
    );

    $key = key($message) ?? "none";
    return new FailureMessage($key, $this->hydrateString($parameters, $message[$key]));
  }

  public function asValid(): Context
  {
    $this->isValid = true;
    return $this;
  }

  public function isValid(): bool
  {
    return $this->isValid;
  }
}
