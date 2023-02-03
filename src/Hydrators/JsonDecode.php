<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\ErrorCode;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\HydratorException;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class JsonDecode implements IHydratorAttribute
{
  public function __construct()
  {
  }

  public function process(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withError(ErrorCode::JSON_INVALID_TYPE);
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

      return $context->withError(ErrorCode::JSON_INVALID_TYPE);

    } catch (\JsonException|HydratorException $e) {
      return $context->withError(ErrorCode::JSON_ERROR, ["message" => $e->getMessage()]);
    }
  }
}
