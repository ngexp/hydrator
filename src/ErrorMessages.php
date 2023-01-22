<?php

declare(strict_types=1);

use Ngexp\Hydrator\ErrorCode;

return [
  ErrorCode::ALNUM => "{className}::{propertyName} property must contain a valid alpha numeric string value, got {value}.",
  ErrorCode::ALPHA => "{className}::{propertyName} must have a valid alphabetic string value, got {value}.",
  ErrorCode::ARRAY => "{className}::{propertyName} value is not of type array.",
  ErrorCode::AUTO => "{className}::{propertyName} type {expectedType}, can not be auto casted.",
  ErrorCode::BETWEEN => "{className}::{propertyName} must have a value between {min} and {max}, got {value}.",
  ErrorCode::BLANK => "{className}::{propertyName} cannot be empty or contain whitespaces only, got {value}.",
  ErrorCode::CHILD_OF_PARENT => "{parentClassName}::{message}",
  ErrorCode::CLASS_ERROR => "Error in the instance of {classType}.",
  ErrorCode::CLASS_NAME => "{className} does not exist.",
  ErrorCode::COERCE => "{className}::{propertyName} cannot be coerced to {type}, got {valueType} with value {value}.",
  ErrorCode::DIGIT => "{className}::{propertyName} must contain a valid numeric string value, got {value}.",
  ErrorCode::EMAIL => "{className}::{propertyName} must contain a valid email address as a string value, got {value}.",
  ErrorCode::EMPTY => "{className}::{propertyName} cannot be empty, got {value}.",
  ErrorCode::ENUM => "{className}::{propertyName} enum does not have a case called \"{value}\"",
  ErrorCode::EXPECTED_TYPE => "{className}::{propertyName} expected a value of type {expectedType}, got {valueType}.",
  ErrorCode::GRAPH => "{className}::{propertyName} must contain a string with consistent visibly printable characters, got {value}.",
  ErrorCode::HASHMAP => "{className}::{propertyName} value is not a hashmap of consistent [{keyName}, {valueName}] pairs.",
  ErrorCode::HASH_KEY_NAME => "{className}::{propertyName} value must contain a hash key named \"{keyName}\" on each row in the array.",
  ErrorCode::HASH_VALUE_NAME => "{className}::{propertyName} value must contain a hash value named \"{valueName}\" on each row in the array.",
  ErrorCode::INVALID_TYPE => "{className}::{propertyName} value must be of type {type}.",
  ErrorCode::INVOKABLE => "{className} does not have an invokable method.",
  ErrorCode::JSON_ERROR => "{className}::{propertyName} value returned the following json error: \"{message}\".",
  ErrorCode::JSON_INVALID_TYPE => "{className}::{propertyName} is not of type array or class.",
  ErrorCode::LARGE => "{className}::{propertyName} can not be greater than {max}, got {value}.",
  ErrorCode::MATCH => "{className}::{propertyName} did not match regex pattern \"{pattern}\", got {value}.",
  ErrorCode::NEGATIVE => "{className}::{propertyName} must contain a negative type of int or float, got {value}.",
  ErrorCode::NEGATIVE_OR_ZERO => "{className}::{propertyName} must contain a negative number or zero, got {value}.",
  ErrorCode::NULL => "{className}::{propertyName} expected non nullable type {expectedType}, got null.",
  ErrorCode::POSITIVE => "{className}::{propertyName} value must be a positive number, got {value}.",
  ErrorCode::PROP_ERROR => "Error in the property {propertyName} of type {className}[].",
  ErrorCode::REQUIRED => "{className}::{propertyName} is required, value is missing.",
  ErrorCode::SMALL => "{className}::{propertyName} can not be less than {min}, got {value}.",
  ErrorCode::STRING => "{className}::{propertyName} value is not of type string.",
];
