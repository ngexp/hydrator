<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionType;

class ResolvedProperty
{
  const SET_BY_PROPERTY = 0;
  const SET_BY_METHOD = 1;

  private bool $isEnum = false;
  private bool $isClass = false;
  private string $type = "";

  /**
   * @param string                                   $name
   * @param \ReflectionType|null                     $reflectionType
   * @param int                                      $setBy
   * @param bool                                     $isOptional
   * @param array<int, \ReflectionAttribute<object>> $attributes
   */
  public function __construct(
    private readonly string          $name,
    private readonly ?ReflectionType $reflectionType,
    #[ExpectedValues([self::SET_BY_PROPERTY, self::SET_BY_METHOD])]
    private readonly int             $setBy,
    private bool                     $isOptional,
    private array                    $attributes
  )
  {
    $this->typeCheck($this->reflectionType);
  }

  public function typeCheck(ReflectionType $reflectionType): void
  {
    if ($reflectionType instanceof ReflectionNamedType) {
      $this->type = $reflectionType->getName();
    } else {
      $this->type = "mixed";
    }

    if (class_exists($this->type)) {
      $rc = new ReflectionClass($this->type);
      if ($rc->isEnum()) {
        $this->isEnum = true;
      } else {
        $this->isClass = true;
      }
    }
  }

  /**
   * @param string $name
   *
   * @return mixed
   * @throws \ReflectionException
   */
  public function resolveEnumCase(string $name): mixed
  {
    $enum = new ReflectionEnum($this->getType());
    foreach ($enum->getCases() as $case) {
      $caseName = $case->getName();
      if ($name === $caseName) {
        return $case->getValue();
      }
    }
    return null;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getPropertyName(): string
  {
    if (str_starts_with($this->name, "set")) {
      return lcfirst(substr($this->name, 3));
    }

    return $this->name;
  }

  #[Pure]
  public function getType(): string
  {
    return $this->type;
  }

  public function allowsNull(): bool
  {
    return !is_null($this->reflectionType) && $this->reflectionType->allowsNull();
  }

  public function getSetBy(): int
  {
    return $this->setBy;
  }

  public function updateOptional(bool $value): void
  {
    $this->isOptional = $this->isOptional || $value;
  }

  public function isClass(): bool
  {
    return $this->isClass;
  }

  public function isEnum(): bool
  {
    return $this->isEnum;
  }

  public function isOptional(): bool
  {
    return $this->isOptional;
  }

  /**
   * @return array<int, mixed>
   */
  public function getAttributes(): array
  {
    return $this->attributes;
  }

  /**
   * @param array<int, \ReflectionAttribute<object>> $attributes
   */
  public function addAttributes(array $attributes): void
  {
    $this->attributes = array_merge($this->attributes, $attributes);
  }
}
