<?php
/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types = 1);

require_once '../vendor/autoload.php';

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Asserts as Assert;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;
use Ngexp\Hydrator\Mutators as Mutator;

class HistoryDTO
{
  #[Assert\NotBlank] // Can not be an empty or contain white spaces only
  private string $event;

  #[Assert\Optional, Mutator\CoerceInt, Assert\Max(2022)] // Data is optional, convert type to int, maximum value is 2022
  private int $year;

  #[Mutator\HashMap(keyName: "key", valueName: "value")]  // Create an array hashmap
  private array $extra;

  public function getEvent(): string
  {
    return $this->event;
  }

  // Attributes can also be set on the set method
  public function setEvent(string $event): void
  {
    $this->event = $event;
  }

  public function getYear(): int
  {
    return $this->year;
  }

  public function setYear(int $year): void
  {
    $this->year = $year;
  }

  public function getExtra(): array
  {
    return $this->extra;
  }

  public function setExtra(array $extra): void
  {
    $this->extra = $extra;
  }
}

class UserDTO
{
  #[Mutator\Trim, Assert\Between(max: 45)]
  private string $name;

  #[Assert\Min(10)]
  private int $age;

  #[Mutator\ArrayOfClassType(HistoryDTO::class)]
  private array $histories;

  private HistoryDTO $history;

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function getAge(): int
  {
    return $this->age;
  }

  public function setAge(int $age): void
  {
    $this->age = $age;
  }

  public function getHistories(): array
  {
    return $this->histories;
  }

  public function setHistories(array $histories): void
  {
    $this->histories = $histories;
  }

  public function getHistory(): HistoryDTO
  {
    return $this->history;
  }

  public function setHistory(HistoryDTO $history): void
  {
    $this->history = $history;
  }
}

$json = <<<JSON
{
  "name": "John Doe",
  "age": 33,
  "histories": [
    {
      "event": "event 1",
      "year": 2012,
      "extra": [
        {
          "key": "Key 1",
          "value": "Value 1"
        },
        {
          "key": "Key 2",
          "value": "Value 2"
        }
      ]
    },
    {
      "event": "event 2",
      "year": 2017,
      "extra": []
    }
  ],
  "history": {
    "event": "Special event",
    "year": 1993,
    "extra": []
  }
}
JSON;

try {
  $hydrator = new Hydrator(UserDTO::class);
  $class = $hydrator->hydrate(new JsonAdapter($json));
  var_dump($class);
} catch (HydratorException $e) {
  echo $e->generateReport();
}
