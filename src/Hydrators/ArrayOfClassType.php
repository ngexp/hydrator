<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Hydrators;

use Attribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\IHydratorAttribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\Traits\Reflection;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class ArrayOfClassType extends MessageHandler implements IHydratorAttribute
{
  use Reflection;

  const PROP_ERROR = "ClassType::PROP_ERROR";
  const INVALID_TYPE = "ArrayOfClassType::INVALID_TYPE";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::PROP_ERROR => "Error in the property \"{propertyName}\" of type {classType}[].",
    self::INVALID_TYPE => "The \"{propertyName}\" property value must be of type array.",
  ];

  /**
   * @param class-string          $classType
   * @param array<string, string> $messageTemplates
   */
  public function __construct(private readonly string $classType, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function hydrateValue(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_array($value)) {
      return $context->withFailure($this->template(self::INVALID_TYPE));
    }

    $resolvedProperties = $this->resolveProperties($this->classType);

    $result = [];
    foreach ($value as $item) {
      $classContext = new Context(null, $item);
      $classType = new ClassType($this->classType);
      $classType->setResolvedProperties($resolvedProperties);
      $classContext = $classType->hydrateValue($classContext);
      if (!$classContext->isValid()) {
        $context->withFailure($this->template(self::PROP_ERROR), ["classType" => $this->classType]);
        $context->inheritFailState($classContext);
        return $context;
      }
      $result[] = $classContext->getValue();
    }

    return $context->withValue($result);
  }
}
