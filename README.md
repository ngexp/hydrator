[![PHPstan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)
[![PHP Composer](https://github.com/ngexp/hydrator/actions/workflows/php.yml/badge.svg)](https://github.com/ngexp/hydrator/actions/workflows/php.yml)

# Ngexp/hydrator

Minimum supported php version is 8.1

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

## Installation
Install the latest version with

```
composer require ngexp/hydrator
```

## Basic Usage

```php
<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Docs;

require_once '../../vendor/autoload.php';

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\Min;
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;

// The data we want to hydrate the instance with.
$json = <<<JSON
{
  "name": "John Doe",
  "age": 20
}
JSON;

// The class has the same data structure as the json data.
class User
{
  public string $name;
  #[Min(30)]
  public int $age;
}

try {
  // We create a new instance of the class by specifying its class name.
  $hydrator = new Hydrator(User::class);
  // Hydrate using the json adapter.
  $class = $hydrator->hydrate(new JsonAdapter($json));

  var_dump($class);

} catch (HydratorException $e) {
  echo $e->getMessage();
}
```

Running this will print out an error:
```
Ngexp\Hydrator\Docs\User::age can not be less than 30, got 20.
```

We have an attribute set on the age property called Min, this is a constraint attribute that verifies
that the value is at least 30, but the JSON value for age is only 20.

The hydrator expect strict types, but we have a couple of attributes that can automatically convert to a specific type if possible, in this case adding the following attribute before the age property will get rid of the exception:

```php
#[AutoCast]
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

| Attribute        | Description                                           | Arguments                    | Error codes it can return      |
|:-----------------|:------------------------------------------------------|:-----------------------------|--------------------------------|
| Alnum            | String must only contain alpha numeric characters     | errorCode, message           | ALNUM                          |
| Alpha            | String must only contain alphabetic characters        | errorCode, message           | ALPHA                          |
| Between          | String or array must be between min and max size      | min, max, errorCode, message | INVALID_TYPE, BETWEEN          |
| CustomConstraint | Custom Constraint, invoked from a class               | className                    | CLASS_NAME, INVOKABLE          |
| Digit            | String must only contain numbers                      | errorCode, message           | DIGIT                          |
| Email            | String must be an email addresses                     | errorCode, message           | EMAIL                          |
| Graph            | String must only contain visibly printable characters | errorCode, message           | GRAPH                          |
| Max              | Number must be less than or equal to max              | max, errorCode               | INVALID_TYPE, TOO_LARGE        |
| Min              | Number must be greater than or equal to min           | min, errorCode               | INVALID_TYPE, TOO_SMALL        |
| Negative         | Number must be less than 0                            | errorCode, message           | INVALID_TYPE, NEGATIVE         |
| NegativeOrZero   | Number must be less than or equal to 0                | errorCode, message           | INVALID_TYPE, NEGATIVE_OR_ZERO |
| NotBlank         | String can not be empty or contain white spaces only  | errorCode, message           | BLANK, STRING                  | 
| NotEmpty         | String or array can not be of size 0                  | errorCode, message           | EMPTY                          |
| Optional         | Data for property is optional                         |                              |                                |
| Pattern          | String must match regex pattern                       | pattern, errorCode           | STRING, NO_MATCH               |
| Positive         | Number must be greater than 0                         | errorCode, message           | INVALID_TYPE, POSITIVE         |
| PositiveOrZero   | Number must be greater than or equal to 0             | errorCode, message           | INVALID_TYPE, POSITIVE_OR_ZERO | 

All constraints return an errorCode as part of the error message, if you need to override this with a custom message, you can set 
your own unique error code here. Use the message parameter to override the default error message if needed.

### Hydration attributes
Hydration attributes, transform the input value in some way.

| Attribute         | Description                                                      | Arguments          | Error codes it can return                      | 
|:------------------|:-----------------------------------------------------------------|:-------------------|------------------------------------------------|
| ArrayOfClassType  | Hydrate array with the specified class type                      | classType          | INVALID_TYPE                                   |
| AutoCast          | Auto casts simple primitive types to int, float, bool and string |                    | AUTO                                           |
| ClassType         | Cast class to specified class type                               | classType          | EXPECTED_TYPE, INVALID_TYPE, NULL, REQUIRED    |
| CoerceBool        | Convert value to a bool type, can be 1, 0, on, of, true, false   |                    | COERCE                                         |
| CoerceFloat       | Convert value to float                                           |                    | COERCE                                         |
| CoerceInt         | Convert value to int                                             |                    | COERCE                                         |
| CoerceString      | Convert value to string                                          |                    | COERCE                                         |
| CustomHydrator    | Custom hydrator, invoked from a Class                            | className          | CLASS_NAME, INVOKABLE                          | 
| HashMap           | Converts a tuple into an array hash map                          | keyName, valueName | ARRAY, HASHMAP, HASH_KEY_NAME, HASH_VALUE_NAME |
| JsonDecode        | Converts a json string into a class or array type                |                    | ARRAY, JSON_ERROR, JSON_INVALID_TYPE           |
| LeftTrim          | Trim left side of string with specified characters               | characters,        | STRING                                         |
| LowerCase         | Converts alpha characters to lower case characters               |                    | STRING                                         |
| RightTrim         | Trim right side of string from specified characters              | characters,        | STRING                                         |
| Trim              | Trim string before hydration                                     |                    | STRING                                         |
| UpperCase         | Converts alpha characters to upper case characters               |                    | STRING                                         |


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
