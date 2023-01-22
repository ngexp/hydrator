<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Adapters;

use Ngexp\Hydrator\HydratorException;
use JsonException;

class JsonAdapter extends HydrationAdapter
{
  /**
   * Returns converted json string to data to hydrate with.
   *
   * @return array<string, mixed>
   * @throws \JsonException
   */
  public function getHydrationData(): array
  {
    $hydrationData = json_decode($this->hydrationData, true, 512, JSON_THROW_ON_ERROR);

    return $this->prepareData($hydrationData);
  }
}
