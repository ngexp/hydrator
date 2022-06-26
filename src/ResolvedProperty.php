<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

namespace Ngexp\Hydrator;

use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;
use ReflectionNamedType;
use ReflectionType;

class ResolvedProperty
{
  const SET_BY_PROPERTY = 0;
  const SET_BY_METHOD = 1;

  /**
   * @param string                           $name
   * @param \ReflectionType|null             $reflectionType
   * @param int                              $setBy
   * @param bool                             $isOptional
   * @param array<int, \ReflectionAttribute<object>> $attributes
   */
  public function __construct(
    private string          $name,
    private ?ReflectionType $reflectionType,
    #[ExpectedValues([self::SET_BY_PROPERTY, self::SET_BY_METHOD])]
    private int             $setBy,
    private bool            $isOptional,
    private array           $attributes
  )
  {
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
    if ($this->reflectionType instanceof ReflectionNamedType) {
      return $this->reflectionType->getName();
    }
    return "mixed";
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
