[![PHPstan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)
[![PHP Composer](https://github.com/ngexp/hydrator/actions/workflows/php.yml/badge.svg)](https://github.com/ngexp/hydrator/actions/workflows/php.yml)

# Ngexp/hydrator

This library hydrates and instantiates classes. Attributes allow you to modify, convert and validate the data before it hydrates the properties. There are two types of attributes, the hydrator attribute and the constraint attribute. The hydrator attribute modifies data and the constraint attribute validates data. Here is a small taste of the features this library offers:

✅ &nbsp;Many attributes available to modify and validate data  
✅ &nbsp;Reusable hydrator for the same class type using memoized reflection for speed    
✅ &nbsp;Easily extendable with new attributes   
✅ &nbsp;Easily extendable with new adapters for hydration data  
✅ &nbsp;Strict type checking  
✅ &nbsp;Hydrate to any depth  
✅ &nbsp;Error messages can be modified   

<hr />

## Table of contents

- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [The constructor is not invoked](#the-constructor-is-not-invoked)
- [Attributes](#attributes)
- [Adapters](#adapters)
- [A more advanced example](#a-more-advanced-example)
- [All hydration failures are reported simultaneously](#all-hydration-failures-are-reported-simultaneously) 

## Installation
Install the latest version with

```
composer require ngexp/hydrator
```

## Basic Usage

```php
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
```

This will print out the following to the screen 
```
class User#12 (2) {
  public string $name =>
  string(8) "John Doe"
  public int $age =>
  int(33)
}
```

Json data that doesn't match the class structure is ignored. Even in this basic form you get type checking, changing age to a string "33" will throw the following message:
```
Hydration failed.
Error in the instance of User.
  The "age" property expected a value of type int, got a value of type string.
```

The hydrator expect strict types, but we have a couple of attributes that can automatically convert to a specific type if possible, in this case adding the following attribute before the age property will get rid of the exception:

```php
#[CoerceInt]
private int $age;
```

## The constructor is not invoked
When the hydrator instantiate the class, it does so without invoking the class constructor. This opens up the possibility to declare our properties directly in the constructor without needing to supply the data at the instantiation moment. 

```php
class User
{
  public function __construct(public string $name, #[CoerceInt] public int $age)
  {
  }
}
```
## Snake case to camel case
Property names are often formatted as snake_case in things like database columns and similar. Those are automatically converted to the camelCase naming convention through the supplied adapters.

So user_id is converted to userId, user_has_email is converted to userHasEmail etc.

## Attributes
Different types of attributes are available, constraint attributes that limits what can be hydrated and hydration attributes that tells how it should be hydrated.

### Constraint attributes
Constraint attributes validate input values.

| Attribute      | Description                                           | Parameters        |
|:---------------|:------------------------------------------------------|:------------------|
| Alnum          | String must only contain alpha numeric characters     | message           |
| Alpha          | String must only contain alphabetic characters        | message           |
| Between        | String or array must be between min and max size      | min, max, message |
| Digit          | String must only contain numbers                      | message           |
| Email          | String must be an email addresses                     | message           |
| Graph          | String must only contain visibly printable characters | message           |
| Max            | Number must be less than or equal to max              | max, message      |
| Min            | Number must be greater than or equal to min           | min, message      |
| Negative       | Number must be less than 0                            | message           |
| NegativeOrZero | Number must be less than or equal to 0                | message           |
| NotBlank       | String can not be empty or contain white spaces only  | message           | 
| NotEmpty       | String or array can not be of size 0                  | message           |
| Optional       | Data for property is optional                         |                   |
| Pattern        | String must match regex pattern                       | pattern, message  |
| Positive       | Number must be greater than 0                         | message           |
| PositiveOrZero | Number must be greater than or equal to 0             | message           | 

Use the message parameter to override the default error message if needed.

Example:
```php
#[Between(min: 2, max: 30, message: [Between::NOT_BETWEEN => "Size of name must be between {min} and {max}"])]
private string $name;
```

### Hydration attributes
Hydration attributes, transform the input value in some way.

| Attribute        | Description                                                      | Parameters                  | 
|:-----------------|:-----------------------------------------------------------------|:----------------------------|
| ArrayOfClassType | Hydrate array with the specified class type                      | classType, message          |
| AutoCast         | Auto casts simple primitive types to int, float, bool and string | message                     |
| ClassType        | Cast class to specified class type                               | classType, message          |
| CoerceBool       | Convert value to a bool type, can be 1, 0, on, of, true, false   | message                     |
| CoerceFloat      | Convert value to float                                           | message                     |
| CoerceInt        | Convert value to int                                             | message                     |
| CoerceString     | Convert value to string                                          | message                     |
| HashMap          | Converts a tuple into an array hash map                          | keyName, valueName, message |
| JsonDecode       | Converts a json string into a class or array type                | message                     |
| LeftTrim         | Trim left side of string with specified characters               | characters, message         |
| LowerCase        | Converts alpha characters to lower case characters               | message                     |
| RightTrim        | Trim right side of string from specified characters              | characters, message         |
| Trim             | Trim string before hydration                                     | message                     |
| UpperCase        | Converts alpha characters to upper case characters               | message                     |

Use the message parameter to override the default error message if needed.

## Adapters
To hydrate from different types of data, we supply two adapters at this time.

Json adapter

```php
$class = $hydrator->hydrate(new JsonAdapter($json));
```
Array adapter

```php
$class = $hydrator->hydrate(new ArrayAdapter($array));
```

## A more advanced example

```php
<?php

declare(strict_types = 1);

require_once '../../vendor/autoload.php';

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\Between;
use Ngexp\Hydrator\Constraints\Max;
use Ngexp\Hydrator\Constraints\Min;
use Ngexp\Hydrator\Constraints\NotBlank;
use Ngexp\Hydrator\Constraints\Optional;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\Hydrators\ArrayOfClassType;
use Ngexp\Hydrator\Hydrators\CoerceInt;
use Ngexp\Hydrator\Hydrators\HashMap;
use Ngexp\Hydrator\Hydrators\Trim;
use Ngexp\Hydrator\HydratorException;

class HistoryDTO
{
  #[NotBlank] // Can not be an empty or contain white spaces only
  private string $event;

  #[Optional, CoerceInt, Max(2022)] // Data is optional, convert type to int, maximum value is 2022
  private int $year;

  #[HashMap(keyName: "key", valueName: "value")]  // Create an array hashmap
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
  #[Trim, Between(max: 45)]
  private string $name;

  #[Min(10, [Min::TOO_SMALL => "User must be at least {min} years old."])]
  private int $age;

  #[ArrayOfClassType(HistoryDTO::class)]
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
```

## All hydration failures are reported simultaneously

The library will try and hydrate everything before it reports any errors. If we change around some things in the json above to trigger some errors:
```
{
  "name": 44,
  ...
        "extra": [
        {
          "ky": "Key 1",
          ...
          ...
          "exra": []
```

We'll get a list of errors looking like this:
```
Hydration failed.
Error in the instance of UserDTO.
	The "name" property is not of type string, could not trim string.
Error in the property "histories" of type HistoryDTO[].
Error in the instance of HistoryDTO.
	The "extra" property value must contain a hash key named "key" on each row in the array.
```
