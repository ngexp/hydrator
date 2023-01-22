<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\HydratorException;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class JsonDecode extends MessageHandler implements IHydratorAttribute
{
  const INVALID_JSON = "JsonDecode::INVALID_JSON";
  const INVALID_TYPE = "JsonDecode::INVALID_TYPE";

  protected array $messageTemplates = [
    self::INVALID_JSON => "The \"{propertyName}\" property value returned the following json error: \"{message}\".",
    self::INVALID_TYPE => "The \"{propertyName}\" property is not of type array or class."
  ];

  /**
   * @param array<string, string> $messageTemplates
   */
  public function __construct(array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function hydrateValue(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withFailure($this->useTemplate(self::INVALID_TYPE));
    }

    try {
      $expectedType = $context->getExpectedType();
      if (class_exists($expectedType)) {
        $hydrator = new Hydrator($expectedType);
        return $context->withValue($hydrator->hydrate(new JsonAdapter($value)));
      }

      if ($expectedType === Type::ARRAY) {
        $json = new JsonAdapter($value);
        return $context->withValue($json->getHydrationData());
      }

      return $context->withFailure($this->useTemplate(self::INVALID_TYPE));

    } catch (HydratorException $e) {
      return $context->withFailure($this->useTemplate(self::INVALID_JSON), ["message" => $e->getMessage()]);
    }
  }
}
