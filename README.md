[![PHPstan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)
[![PHP Composer](https://github.com/ngexp/hydrator/actions/workflows/php.yml/badge.svg)](https://github.com/ngexp/hydrator/actions/workflows/php.yml)

# Ngexp/hydrator

## Overview

Ngexp/hydrator is a library that allows you to hydrate data into an object. Hydration is the process of populating an
object with data from a source such as an array or JSON. The library makes use of attributes to add additional behavior
to the process, such as converting data from one type to another, validating data, and more.

The library requires PHP 8.1 or higher and supports strict type checking while still allowing mixed data types. Some of its
features include:

✅ &nbsp;Various attributes for modifying and validating data  
✅ &nbsp;Reusable hydration using memoized reflection for improved speed  
✅ &nbsp;Extendable with new attributes and adapters for hydration data  
✅ &nbsp;Ability to hydrate to any depth  
✅ &nbsp;Modifiable error messages   

<hr />

## Table of contents

- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Attribute Order Matter](#attribute-order-matter)
- [The constructor is not invoked](#the-constructor-is-not-invoked)
- [Attributes](#attributes)
- [Hydrating from Different Sources](#hydrating-from-different-sources)

## Installation
To install the latest version of the library, run the following command:

```
composer require ngexp/hydrator
```

## Basic Usage
Here's an example of how to use the library to hydrate a class:

```php
<?php

declare(strict_types = 1);

namespace Ngexp\Hydrator\Docs;

require_once '../../vendor/autoload.php';

use Ngexp\Hydrator\Adapters\JsonAdapter;
use Ngexp\Hydrator\Constraints\Min
use Ngexp\Hydrator\Hydrator;
use Ngexp\Hydrator\HydratorException;

// JSON data to be hydrated into the class
$json = <<<JSON
{
  "name": "John Doe",
  "age": 20
}
JSON;

// Class structure should match the JSON data
class User
{
  public string $name;
  #[Min(30)]
  public int $age;
}

try {
  // Create an instance of the Hydrator class and specify the class name
  $hydrator = new Hydrator(User::class);
  // Hydrate the class using the JSON adapter.
  $class = $hydrator->hydrate(new JsonAdapter($json));

  var_dump($class);

} catch (HydratorException $e) {
  echo $e->getMessage();
}
```

As a demonstration, running the code above will throw a HydrationException:
```
Ngexp\Hydrator\Docs\User::age can not be less than 30, got 20.
```

In this example, the age property has a Min validation attribute set to 30, but the value for age in the JSON data is 20.

The Hydrator requires strict typing, but certain attributes can automatically convert values to a specific type if possible.
For example, if a value is a string, adding the AutoCast attribute will automatically cast it to an integer.

```php
#[AutoCast]
private int $age;
```

You can also narrow it and use CoerceInt instead, both will internally use CoerceInt since the property type is int.

```php
#[CoerceInt]
private int $age;
```

You can also use the CoerceInt attribute to achieve the same result. Both AutoCast and CoerceInt will internally use 
CoerceInt since the property type is an integer.


## The constructor is not invoked
When the Hydrator instantiates a class, it does so without invoking the class constructor. This means that properties
can be declared directly in the constructor without having to supply data at the instantiation moment.

```php
class User
{
  public function __construct(public string $name, #[CoerceInt] public int $age)
  {
  }
}
```
## Snake case to camel case
The Hydrator also converts snake case property names (e.g. `user_id` to `userId`) to camel case automatically through its
adapters.

## Attributes
Different types of attributes are available, constraint attributes that limits and validates what can be hydrated and hydration 
attributes that tells how it should be hydrated.

### Constraint attributes
Validation attributes validate and constrain input values.

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

```php
#[Email(message="Custom error message", errorCode="unique_error_code")]
private string $email;
```

Note that the constraint attributes are executed in the order they are added to the class, so be mindful of the order in which you declare them. 
You can also use multiple constraint attributes on a single property to achieve more complex validations.

All constraints return an errorCode on failure as part of the error message, if you need to override this with a custom message, you can set 
your own unique error code here. Use the message parameter to override the default error message if needed.

### Hydration attributes
Hydration attributes define how the values should be transformed during the hydration process. The AutoCast and CoerceInt attributes mentioned earlier are
examples of hydration attributes. 

You can use hydration attributes to automatically cast strings to integers, floats, or booleans, or to format dates and times. Other hydration attributes can be
used to trim strings, remove HTML tags, or to apply custom transformations.

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


## Attribute Order Matter
It's important to note that the order of attributes matters. The IHydratorAttribute interface can be used for both
hydration and validation, and attributes will be executed in the order they are added. This makes it possible to have
a flexible validation and hydration process.

## Hydrating from Different Sources
The hydrator is equipped with two adapters to hydrate data from different sources.

For hydration from a JSON source, use the `JsonAdapter`:

```php
$class = $hydrator->hydrate(new JsonAdapter($json));
```

And for hydration from an array source, use the `ArrayAdapter`:

```php
$class = $hydrator->hydrate(new ArrayAdapter($array));
```
