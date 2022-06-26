<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class HashMap extends MessageHandler implements IHydratorAttribute
{
  const NOT_ARRAY = "HashMap::NOT_ARRAY";
  const NOT_HASHMAP = "HashMap::NOT_HASHMAP";
  const HASH_KEY_NAME = "HashMap::HASH_KEY_NAME";
  const HASH_VALUE_NAME = "HashMap::HASH_VALUE_NAME";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_ARRAY => "The \"{propertyName}\" property value is not of type array.",
    self::NOT_HASHMAP => "The \"{propertyName}\" property value is not a hashmap of consistent [{keyName}, {valueName}] pairs.",
    self::HASH_KEY_NAME => "The \"{propertyName}\" property value must contain a hash key named \"{keyName}\" on each row in the array.",
    self::HASH_VALUE_NAME => "The \"{propertyName}\" property value must contain a hash value named \"{valueName}\" on each row in the array.",
  ];

  /**
   * @param string                $keyName   The name of the key for the hash map expected from the hydrated data.
   * @param string                $valueName The name of the value for the hash map expected from the hydrated data.
   * @param array<string, string> $messageTemplates
   */
  public function __construct(private string $keyName, private string $valueName, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function hydrateValue(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_array($value)) {
      return $context->withFailure($this->template(self::NOT_ARRAY));
    }

    $result = [];
    foreach ($value as $item) {
      if (!is_array($item) || count($item) !== 2) {
        return $context->withFailure($this->template(self::NOT_HASHMAP), ["keyName" => $this->keyName, "valueName" => $this->valueName]);
      }
      if (!array_key_exists($this->keyName, $item)) {
        return $context->withFailure($this->template(self::HASH_KEY_NAME), ["keyName" => $this->keyName]);
      }
      if (!array_key_exists($this->valueName, $item)) {
        return $context->withFailure($this->template(self::HASH_VALUE_NAME), ["valueName" => $this->valueName]);
      }

      $result[$item[$this->keyName]] = $item[$this->valueName];
    }

    return $context->withValue($result);
  }
}
