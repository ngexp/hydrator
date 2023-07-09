<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator;

use Ngexp\Hydrator\Mutators\ClassType;
use Ngexp\Hydrator\Traits\ReflectionUtils;
use Ngexp\Hydrator\Traits\StringFormatting;

class Context
{
  use ReflectionUtils;
  use StringFormatting;

  private ErrorCollection $errors;
  private bool $isValid = false;
  private ?Context $parentContext = null;

  /**
   * @param \Ngexp\Hydrator\ResolvedProperty|null $property
   * @param mixed                                 $value
   * @param \Ngexp\Hydrator\Mutators\ClassType    $classType
   */
  public function __construct(private readonly ?ResolvedProperty $property,
                              private mixed                      $value,
                              private readonly ClassType         $classType)
  {
    $this->errors = new ErrorCollection();
  }

  public function getClassType(): ClassType
  {
    return $this->classType;
  }

  public function getParentContext(): ?Context
  {
    return $this->parentContext;
  }

  public function setParentContext(Context $context): void
  {
    // Since we are merging contexts, this might not be a parent but a sibling.
    if (!$this->getClassType()->equal($context->getClassType())) {
      $this->parentContext = $context;
    }
  }

  public function getErrors(): ErrorCollection
  {
    return $this->errors;
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

  public function hasProperty(): bool
  {
    return !is_null($this->property);
  }

  public function isClassProperty(): bool
  {
    if (is_null($this->property)) {
      return false;
    }

    return $this->property->isClass();
  }

  public function isValid(): bool
  {
    return $this->isValid;
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
    throw new RuntimeHydrationException("Trying to get class name from a non class type");
  }

  public function getValueType(): string
  {
    return $this->getVariableType($this->value);
  }

  public function inheritState(Context $context): Context
  {
    $this->errors->inheritErrors($context->getErrors());
    $this->isValid = $context->isValid();

    return $this;
  }

  public function withValue(mixed $value): Context
  {
    $this->value = $value;
    $this->isValid = true;
    return $this;
  }

  /**
   * @param string               $errorCode
   * @param array<string, mixed> $extraParameters
   *
   * @return Context
   */
  public function withError(string $errorCode, array $extraParameters = []): Context
  {
    $this->errors->addError(new Error($this, $errorCode, $extraParameters));
    $this->isValid = false;

    return $this;
  }

  /**
   * @param string               $message
   * @param array<string, mixed> $extraParameters
   *
   * @return Context
   */
  public function withErrorMessage(string $message, array $extraParameters = []): Context
  {
    $extraParameters = array_merge($extraParameters, ["internalMessage" => $message]);
    $this->errors->addError(new Error($this, Error::INTERNAL_CUSTOM_MESSAGE, $extraParameters));
    $this->isValid = false;

    return $this;
  }

  public function asValid(): Context
  {
    $this->isValid = true;
    return $this;
  }
}
