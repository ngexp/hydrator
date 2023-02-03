<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\IHydratorAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class HashMap implements IHydratorAttribute
{
  /**
   * @param string $keyName   The name of the key for the hash map expected from the hydrated data.
   * @param string $valueName The name of the value for the hash map expected from the hydrated data.
   */
  public function __construct(
    private readonly string $keyName,
    private readonly string $valueName
  )
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_array($value)) {
      return $context->withError(ErrorCode::ARRAY);
    }

    $result = [];
    foreach ($value as $item) {
      if (!is_array($item) || count($item) !== 2) {
        return $context->withError(ErrorCode::HASHMAP, ["keyName" => $this->keyName, "valueName" => $this->valueName]);
      }
      if (!array_key_exists($this->keyName, $item)) {
        return $context->withError(ErrorCode::HASH_KEY_NAME, ["keyName" => $this->keyName]);
      }
      if (!array_key_exists($this->valueName, $item)) {
        return $context->withError(ErrorCode::HASH_VALUE_NAME, ["valueName" => $this->valueName]);
      }

      $result[$item[$this->keyName]] = $item[$this->valueName];
    }

    return $context->withValue($result);
  }
}
