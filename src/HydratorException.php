<?php

declare(strict_types=1);

namespace Ngexp\Hydrator;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Ngexp\Hydrator\Hydrators\ArrayOfClassType;
use Ngexp\Hydrator\Hydrators\ClassType;

class HydratorException extends Exception
{
  /**
   * @param string                                      $message
   * @param array<int, \Ngexp\Hydrator\FailureMessage> $failureMessages
   */
  public function __construct(string $message, private array $failureMessages = [])
  {
    parent::__construct($message);
  }

  /**
   * @return array<string>
   */
  public function getMessages(): array
  {
    $result = [];

    /** @var \Ngexp\Hydrator\FailureMessage $failureMessage */
    foreach ($this->failureMessages as $failureMessage) {
      $result[] = $failureMessage->getMessage();
    }

    return $result;
  }

  /**
   * @return array<\Ngexp\Hydrator\FailureMessage>
   */
  public function getFailureMessages(): array
  {
    return $this->failureMessages;
  }

  public function generateReport(string $newLine = "\n", bool $development = true): string
  {
    if ($development) {
      $report = $this->getMessage() . $newLine;
      /** @var \Ngexp\Hydrator\FailureMessage $message */
      foreach ($this->failureMessages as $message) {
        $code = $message->getCode();
        $bump =
          ($code != ClassType::CLASS_ERROR && $code !== ClassType::PROP_ERROR && $code !== ArrayOfClassType::PROP_ERROR) ? "\t" : "";
        $report .= $bump . $message->getMessage() . $newLine;
      }
      return $report;
    } else {
      return $this->getMessage() . $newLine;
    }
  }
}
