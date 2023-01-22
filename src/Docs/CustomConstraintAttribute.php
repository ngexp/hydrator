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
  // In this similar example compared to CustomConstraintExample.php, instead of sending a message string, we define
  // a custom error code that we'll return back.
  const FAILED = "NOT_FROM_SCANDINAVIA";

  public function constraint(Context $context): Context
  {
    $value = $context->getValue();
    $value = strtolower(trim($value));

    if ($value !== "denmark" && $value !== "norway" && $value !== "sweden") {
      return $context->withError(self::FAILED);
    }

    return $context->asValid();
  }
}

// We define a list of all custom error messages.
$customErrorMessages = [
  FromScandinavia::FAILED => "{value} is not part of Scandinavia"
];

// The data we want to hydrate the instance with.
$json = <<<JSON
{
  "country": "England"
}
JSON;

class Location {
  #[FromScandinavia]
  public string $country;
}

try {
  // And here we add the custom error messages to the hydrator and let it sort out the message.
  $hydrator = new Hydrator(Location::class, $customErrorMessages);
  // Hydrate using the json adapter.
  $class = $hydrator->hydrate(new JsonAdapter($json));

  var_dump($class);

} catch (HydratorException $e) {
  echo $e->generateReport();
}
