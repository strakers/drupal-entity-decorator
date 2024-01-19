<?php

namespace Drupal\entity_decorator_api\Support\Utility;

/*
 * =============================================================================
 * [ CLASS UTILITY FUNCTIONS ]
 * =============================================================================
 */

function class_uses_deep(string $class, bool $autoload = true): array {
  $traits = [];

  // get traits of class and parent classes
  do {
    $traits = array_merge(class_uses($class, $autoload), $traits);
  }
  while($class = get_parent_class($class));

  // get parent traits of traits


  // return list of traits
  return $traits;
}

function has_trait(string $class, string $trait, bool $autoload = true): bool {
  return in_array($trait, class_uses_deep($class, $autoload));
}

function has_interface(string $class, string $interface, bool $autoload = true): bool {
  return in_array($interface, class_implements($class, $autoload));
}

/*
 * =============================================================================
 * [ STRING UTILITY FUNCTIONS ]
 * =============================================================================
 */

function toJoinedUpperCase(string $string, $lowerCaseFirst = true): string {
  $string = ucwords($string, '_- ');
  if ($lowerCaseFirst) {
    $string[0] = strtolower($string[0]);
  }
  return preg_replace("/[_\-\s]/", '', $string);
}

function toCamelCase(string $string, $lowerCaseFirst = true): string {
  return toJoinedUpperCase($string, true);
}

function toPascalCase(string $string, $lowerCaseFirst = true): string {
  return toJoinedUpperCase($string, false);
}

function toSkewerCase(string $string, $useDashSeparator = false): string {
  $separator = $useDashSeparator ? '-' : '_';
  $string = preg_replace("/[_\-\s]/", $separator, $string);
  return strtolower(preg_replace('/(?<!^)[A-Z]/', $separator . '$0', $string));
}

function toKebabCase(string $string): string {
  return toSkewerCase($string, true);
}

function toSnakeCase(string $string): string {
  return toSkewerCase($string, false);
}