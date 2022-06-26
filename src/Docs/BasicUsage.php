<?php

declare(strict_types = 1);

require_once '../../vendor/autoload.php';

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;

// The data we want to hydrate the instance with.
$json = <<<JSON
{
  "name": "John Doe",
  "age": 33
}
JSON;

// The class has the same data structure as the json data.
class User
{
  public string $name;
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
