<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;
use ReflectionEnum;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;

class ResolvedProperty
{
  const SET_BY_PROPERTY = 0;
  const SET_BY_METHOD = 1;

  private string $typeName;

  /** @var array<int|string, mixed> */
  private array $enumCases = [];

  /**
   * @param string                                   $name
   * @param \ReflectionType|null                     $reflectionType
   * @param \Ngexp\Hydrator\TypeOf                   $typeOf
   * @param int                                      $setBy
   * @param bool                                     $isOptional
   * @param array<int, \ReflectionAttribute<object>> $attributes
   *
   * @throws \ReflectionException
   */
  public function __construct(
    private readonly string          $name,
    private readonly ?ReflectionType $reflectionType,
    private readonly TypeOf          $typeOf,
    #[ExpectedValues([self::SET_BY_PROPERTY, self::SET_BY_METHOD])]
    private readonly int             $setBy,
    private bool                     $isOptional,
    private array                    $attributes
  )
  {
    $this->typeName = $this->reflectTypeName();
    if ($this->typeOf === TypeOf::EnumType) {
      $this->enumCases = $this->reflectEnumCases($this->reflectionType);
    }
  }

  /**
   * @param string $name
   *
   * @return mixed
   */
  public function resolveEnumCase(string $name): mixed
  {
    if (isset($this->enumCases[$name])) {
      return $this->enumCases[$name];
    }
    return null;
  }

  public function hasType(string $valueType): bool
  {
    if ($this->reflectionType instanceof ReflectionNamedType) {
      if ($valueType === $this->reflectionType->getName()) {
        return true;
      }
    } else if ($this->reflectionType instanceof ReflectionUnionType) {
      foreach ($this->reflectionType->getTypes() as $type) {
        if ($type === $type->getName()) {
          return true;
        }
      }
    }
    return false;
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
    return $this->typeName;
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
    return $this->typeOf === TypeOf::ClassType;
  }

  public function isEnum(): bool
  {
    return $this->typeOf === TypeOf::EnumType;
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

  private function reflectTypeName(): string
  {
    if (!$this->reflectionType) {
      return Type::MIXED;
    }

    if ($this->reflectionType instanceof ReflectionNamedType) {
      return $this->reflectionType->getName();
    } else if ($this->reflectionType instanceof ReflectionUnionType) {
      $unionTypes = [];
      foreach ($this->reflectionType->getTypes() as $type) {
        $unionTypes[] = $type->getName();
      }
      return implode("|", $unionTypes);
    } else if ($this->reflectionType instanceof ReflectionIntersectionType) {
      $unionTypes = [];
      foreach ($this->reflectionType->getTypes() as $type) {
        if ($type instanceof ReflectionNamedType) {
          $unionTypes[] = $type->getName();
        }
      }
      return implode("&", $unionTypes);
    }

    // Should not happen.
    throw new RuntimeException("Ngexp\\Hydrator internal error");
  }

  /**
   * @param \ReflectionType|null $reflectionType
   *
   * @return array<int|string, mixed>
   * @throws \ReflectionException
   */
  public function reflectEnumCases(?ReflectionType $reflectionType): array
  {
    $enumCases = [];
    if ($reflectionType instanceof ReflectionNamedType) {
      $enum = new ReflectionEnum($this->getType());
      foreach ($enum->getCases() as $case) {
        $enumCases[$case->getName()] = $case->getValue();
      }
    }
    return $enumCases;
  }
}
