<?php

namespace Drupal\entity_decorator\Support\DataType;

use loophp\collection\Collection;
use loophp\collection\Contract\Collection as CollectionInterface;

class EntityDecoratorCollection {

  protected CollectionInterface $collection;

  public static function fromIterable(iterable $iterable): static {
    return new static($iterable);
  }

  public function __construct(iterable $iterable) {
    if ($iterable instanceof CollectionInterface) {
      $this->collection = $iterable;
    }
    else {
      $this->collection = Collection::fromIterable($iterable)->strict();
    }
  }

  public function __call($name, $arguments) {
    return $this->{$name}(...$arguments);
  }

  public function search(callable ...$callbacks) {
    return $this->filter($callbacks)->first()->current(0, null);
  }
}