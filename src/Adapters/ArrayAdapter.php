<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Adapters;

class ArrayAdapter extends HydrationAdapter
{
  /**
   * Returns data to hydrate with.
   *
   * @return array<string, mixed>
   */
  public function getHydrationData(): array
  {
    return $this->prepareData($this->hydrationData);
  }
}
