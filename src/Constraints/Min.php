<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Min extends MessageHandler implements IConstraintAttribute
{
  const NOT_A_NUMBER = "Min::NOT_A_NUMBER";
  const TOO_SMALL = "Min::TOO_SMALL";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_A_NUMBER => "The value type for the \"{propertyName}\" property must be of type int or float, got {valueType}.",
    self::TOO_SMALL => "The \"{propertyName}\" property can not be less than {min}, got {value}."
  ];

  /**
   * @param int                   $min
   * @param array<string, string> $messageTemplates
   */
  public function __construct(private readonly int $min = 0, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function constraint(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_int($value) && !is_float($value)) {
      return $context->withFailure($this->useTemplate(self::NOT_A_NUMBER));
    }
    if ($value < $this->min) {
      return $context->withFailure($this->useTemplate(self::TOO_SMALL), ["min" => $this->min]);
    }

    return $context->asValid();
  }
}
