<?php

namespace Drupal\entity_decorator\Support\DataType;

use loophp\collection\Collection;
use loophp\collection\Operation\Filter;
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
    if (method_exists($this->collection, $name)) {
      if (in_array($name, ['all', 'current', 'first', 'last'])) {
        return $this->collection->{$name}(...$arguments);
      }
      else {
        $this->collection = $this->collection->{$name}(...$arguments);
        return $this;
      }
    }
    return null;
  }

  public function search(callable ...$callbacks) {
    $this->collection = Collection::fromCallable((new Filter())()(...$callbacks), [$this->collection]);
    return $this->collection->first()->current(0, null);
  }

  public function values(): static {
    $arr = $this->collection->all();
    $this->collection = Collection::fromIterable(array_values($arr))->strict();
    return $this;
  }
}