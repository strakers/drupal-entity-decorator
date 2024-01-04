<?php

namespace Drupal\entity_decorator\Support\Utility;

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