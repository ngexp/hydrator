<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types = 1);

namespace Ngexp\Hydrator\Docs;

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Context;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use Ngexp\Hydrator\Hydrators\CustomHydrator;

require_once '../../vendor/autoload.php';

// Will decrease a value with 10, to a minimum of 0
class FountainOfYouth {
  public function __invoke(Context $context): Context
  {
    $value = $context->getValue();
    $value = max(0, $value - 10);

    return $context->withValue($value);
  }
}

$json = <<<JSON
{
  "name": "John Doe",
  "age": "33"
}
JSON;

class User
{
  public string $name;

  #[CustomHydrator(FountainOfYouth::class)]
  public int $age;
}

try {
  // We create a new instance of the class by specifying its class name.
  $hydrator = new Hydrator(User::class);
  // Hydrate using the json adapter.
  $class = $hydrator->hydrate(new JsonAdapter($json));

  var_dump($class);

} catch (HydratorException $e) {
  echo $e->generateReport();
}
