<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Pattern extends MessageHandler implements IConstraintAttribute
{
  const NOT_A_STRING = "Pattern::NOT_A_STRING";
  const NO_MATCH = "Pattern::NO_MATCH";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_A_STRING => "The \"{propertyName}\" property value is not of type string.",
    self::NO_MATCH => "The \"{propertyName}\" property did not match regex pattern \"{pattern}\", got {value}."
  ];

  /**
   * @param string                $pattern
   * @param array<string, string> $messageTemplates
   */
  public function __construct(public string $pattern, array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function constraint(Context $context): Context
  {
    $value = $context->getValue();
    if (!is_string($value)) {
      return $context->withFailure($this->useTemplate(self::NOT_A_STRING));
    }
    if (!preg_match($this->pattern, $value)) {
      return $context->withFailure($this->useTemplate(self::NO_MATCH), ["pattern" => $this->pattern]);
    }

    return $context->asValid();
  }
}
