<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Type;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Between extends MessageHandler implements IConstraintAttribute
{
  const INVALID_TYPE = "Between::INVALID_TYPE";
  const NOT_BETWEEN = "Between::NOT_BETWEEN";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::INVALID_TYPE => "The value type for the \"{propertyName}\" property must be an array or class.",
    self::NOT_BETWEEN => "The \"{propertyName}\" property must have a value between {min} and {max}, got {value}."
  ];

  /**
   * @param int                   $min
   * @param int                   $max
   * @param array<string, string> $messageTemplates
   */
  public function __construct(public int $min = 0, public int $max = PHP_INT_MAX, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function constraint(Context $context): Context
  {
    $value = $context->getValue();
    $type = $context->getValueType();

    switch ($type) {
      case Type::STRING:
        if (strlen($value) < $this->min || strlen($value) > $this->max) {
          return $context->withFailure($this->template(self::NOT_BETWEEN));
        }
        break;

      case Type::ARRAY:
        if (count($value) < $this->min || count($value) > $this->max) {
          return $context->withFailure($this->template(self::NOT_BETWEEN));
        }
        break;

      default:
        return $context->withFailure($this->template(self::INVALID_TYPE));
    };

    return $context->asValid();
  }
}
