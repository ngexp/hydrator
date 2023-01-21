<?php

declare(strict_types=1);

namespace Ngexp\Hydrator\Docs;

require_once '../../vendor/autoload.php';

use Attribute;
use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use Ngexp\Hydrator\IConstraintAttribute;

// Constraint checks if string contains a scandinavian country name.
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class FromScandinavia implements IConstraintAttribute {
  public function constraint(Context $context): Context
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
  #[FromScandinavia()]
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
