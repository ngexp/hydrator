<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator;

use Exception;

class HydratorException extends Exception
{
  /**
   * @param string                                $message
   * @param \Ngexp\Hydrator\ErrorCollection       $errors
   * @param \Ngexp\Hydrator\ErrorMessageAggregate $errorMessages
   */
  public function __construct(string                                 $message,
                              private readonly ErrorCollection       $errors,
                              private readonly ErrorMessageAggregate $errorMessages)
  {
    parent::__construct($message);
  }

  public function getErrors(): ErrorCollection
  {
    return $this->errors;
  }

  /**
   * @return \Ngexp\Hydrator\ErrorMessageAggregate
   */
  public function getErrorMessages(): ErrorMessageAggregate
  {
    return $this->errorMessages;
  }

  public function generateReport(string $title = "Hydration Error",
                                 string $newLine = "\n",
                                 bool   $debug = true): string
  {
    if ($debug) {
      $report = $title . $newLine;
      foreach ($this->errorMessages as $error) {
        $report .= $error . $newLine;
      }
      return $report;
    } else {
      return $this->getMessage() . $newLine;
    }
  }
}
