<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Constraints;

use Attribute;
use Ngexp\Hydrator\MessageHandler;
use Ngexp\Hydrator\IConstraintAttribute;
use Ngexp\Hydrator\Context;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Graph extends MessageHandler implements IConstraintAttribute
{
  const NOT_GRAPH = "Graph::NOT_GRAPH";

  /** @var array<string, string> */
  protected array $messageTemplates = [
    self::NOT_GRAPH => "The \"{propertyName}\" property must contain a string with consistent visibly printable characters, got {value}."
  ];

  /**
   * @param array<string, string> $messageTemplates
   */
  public function __construct(array $messageTemplates = [])
  {
    $this->updateMessageTemplates($messageTemplates);
  }

  public function constraint(Context $context): Context
  {
    $result = ctype_graph($context->getValue());
    if (!$result) {
      return $context->withFailure($this->useTemplate(self::NOT_GRAPH));
    }

    return $context->asValid();
  }
}
