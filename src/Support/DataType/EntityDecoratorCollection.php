<?php

namespace Drupal\entity_decorator\Support\DataType;

use loophp\collection\Collection;
use loophp\collection\Contract\Collection as CollectionInterface;

class EntityDecoratorCollection extends Collection {
  public function values(): CollectionInterface {
    $arr = $this->all();
    return static::fromIterable(array_values($arr));
  }

  public function search(callable ...$callbacks) {
    return $this->filter($callbacks)->first()->current(0, null);
  }
}