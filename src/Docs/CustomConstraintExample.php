<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Docs;

require_once '../../vendor/autoload.php';

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\CustomConstraint;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;

// Fails if specified country isn't from scandinavia.
class FromScandinavia {
  public function __invoke(Context $context): Context
  {
    $value = $context->getValue();
    $value = strtolower(trim($value));

    if ($value !== "denmark" && $value !== "norway" && $value !== "sweden") {
      return $context->withFailure("Not from Scandinavia");
    }

    return $context->asValid();
  }
}

// The data we want to hydrate the instance with.
$json = <<<JSON
{
  "country": "England"
}
JSON;

class Location {
  #[CustomConstraint(FromScandinavia::class)]
  public string $country;
}

try {
  // We create a new instance of the class by specifying its class name.
  $hydrator = new Hydrator(Location::class);
  // Hydrate using the json adapter.
  $class = $hydrator->hydrate(new JsonAdapter($json));

  var_dump($class);

} catch (HydratorException $e) {
  echo $e->generateReport();
}
