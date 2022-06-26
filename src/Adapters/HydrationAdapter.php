<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Adapters;

abstract class HydrationAdapter
{
  public function __construct(protected mixed $hydrationData)
  {
  }

  /**
   * Returns data to hydrate with.
   *
   * @return array<string, mixed>
   */
  public function getHydrationData(): array
  {
    return $this->prepareData($this->hydrationData);
  }

  /**
   * Prepares the hydration data by converting all snake case property names to camel case format.
   *
   * @param mixed $hydrationData
   *
   * @return array<string, mixed>
   */
  protected function prepareData(mixed $hydrationData): array
  {
    if (!is_array($hydrationData)) {
      return [];
    }

    return $this->snakeCaseToCamelCaseAllKeys($hydrationData);
  }

  /**
   * Converts field names from snake case to camel case naming convention.
   *
   * @param array<string, mixed> $hydrationData
   *
   * @return array<string, mixed>
   */
  protected function snakeCaseToCamelCaseAllKeys(array $hydrationData): array
  {
    $newHydrationData = [];
    foreach ($hydrationData as $key => $value) {
      if (is_string($key)) {
        $parts = explode("_", $key);
        if (count($parts) > 1) {
          $key = "";
          foreach ($parts as $part) {
            $key .= ucfirst($part);
          }
          $key = lcfirst($key);
        }
      }
      if (is_array($value)) {
        $value = $this->snakeCaseToCamelCaseAllKeys($value);
      }
      $newHydrationData[$key] = $value;
    }

    return $newHydrationData;
  }
}
