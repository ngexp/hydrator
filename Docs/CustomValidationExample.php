<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

require_once '../vendor/autoload.php';

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\CustomConstraint;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;

// Validator checks if string contains a scandinavian country name.
class IsFromScandinavia {
  public function __invoke(Context $context): Context
  {
    $value = $context->getValue();
    if (! is_string($value)) {
      return $context->withErrorMessage("{value} is not part of Scandinavia");
    }
    $value = strtolower(trim($value));

    if ($value !== "denmark" && $value !== "norway" && $value !== "sweden") {
      // We can just return a message directly if we want to. Less flexible than returning an error code, but simpler.
      return $context->withErrorMessage("{value} is not part of Scandinavia");
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
  #[CustomConstraint(IsFromScandinavia::class)]
  public string $country;
}

try {
  // We create a new instance of the class by specifying its class name.
  $hydrator = new Hydrator(Location::class);
  // Hydrate using the json adapter.
  $class = $hydrator->hydrate(new JsonAdapter($json));

  var_dump($class);

} catch (HydratorException $e) {
  echo $e->getMessage();
}
