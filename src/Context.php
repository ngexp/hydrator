<?php

declare(strict_types=1);


namespace Ngexp\Hydrator;

use http\Message;
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
  public function __construct(private readonly ?ResolvedProperty $property, private mixed $value)
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

  public function inheritFailState(Context $context): Context
  {
    foreach ($context->getFailureMessages() as $failureMessage) {
      $this->failureMessages[] = $failureMessage;
    }
    $this->isValid = $context->isValid();

    return $this;
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
    array_unshift($this->failureMessages, FailureMessage::create($this, $message, $extraParameters));
    $this->isValid = false;
    return $this;
  }

  /**
   * @param array<string, string> $message
   * @param array<string, mixed>  $extraParameters
   *
   * @return Context
   */
  public function withFailure(array|string $message, array $extraParameters = []): Context
  {
    if (is_array($message)) {
      $this->failureMessages[] = FailureMessage::create($this, $message, $extraParameters);
    } else if (is_string($message)) {
      $this->failureMessages[] = FailureMessage::create($this, ['ERROR' => $message], $extraParameters);
    }
    $this->isValid = false;

    return $this;
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
