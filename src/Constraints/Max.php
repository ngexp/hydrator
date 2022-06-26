<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Max extends MessageHandler implements IConstraintAttribute
{
  const NOT_A_NUMBER = "Max::NOT_A_NUMBER";
  const TOO_LARGE = "Max::TOO_LARGE";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_A_NUMBER => "The value type for the \"{propertyName}\" property must be an int or float, got {value}.",
    self::TOO_LARGE => "The \"{propertyName}\" property can not be greater than {max}, got {value}."
  ];

  /**
   * @param int                   $max
   * @param array<string, string> $messageTemplates
   */
  public function __construct(private int $max = 0, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function constraint(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_int($value) && !is_float($value)) {
      return $context->withFailure($this->template(self::NOT_A_NUMBER));
    }
    if ($value > $this->max) {
      return $context->withFailure($this->template(self::TOO_LARGE), ["max" => $this->max]);
    }

    return $context->asValid();
  }
}
