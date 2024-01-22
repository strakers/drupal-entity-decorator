<?php

namespace Drupal\entity_decorator_api\Support\Types;

use Drupal\entity_decorator_api\Base\EntityDecoratorInterface;
use function Drupal\entity_decorator_api\Support\Utility\has_interface;

class Collection implements \ArrayAccess, \Countable, \Iterator {
  public readonly array $items;
  protected readonly array $key_references;
  protected readonly bool $has_sequential_keys;
  protected int $position = 0;
  protected string $collection_type;

  public function __construct(array $items, bool $preserve_keys = true) {
    // determine collection metadata
    $first_item = reset($items);
    $this->has_sequential_keys = array_is_list($items);
    $this->collection_type = is_object($first_item) ? $first_item::class : gettype($first_item);

    // todo: to be implemented at a later time
    /*// ensure array is uniform by stripping non-uniform items
    if (is_object($first_item)) {
      $this->collection_type = $first_item::class;
      $items = array_filter($items, fn($item) => $item::class === $this->collection_type);
    }
    else {
      $this->collection_type = gettype($first_item);
      $items = array_filter($items, fn($item) => gettype($item) === $this->collection_type);
    }*/

    // load items and indexes
    $this->items = $preserve_keys ? $items : array_values($items);
    $this->key_references = array_keys($items);
  }

  /**
   * @inheritDoc
   */
  public function offsetExists(mixed $offset): bool {
    // if array has hybrid mix of associative and numerical keys
    // this allows access to both types of keys
    if (!$this->has_sequential_keys && is_int($offset)) {
      $array = array_values($this->items);
      return isset($array[$offset]);
    }
    return isset($this->items[$offset]);
  }

  /**
   * @inheritDoc
   */
  public function offsetGet(mixed $offset): mixed {
    // if looking for a numerical index on an associative array,
    // convert the array to sequential and get item at the sequential index
    if (!$this->has_sequential_keys && is_int($offset)) {
      $array = array_values($this->items);
      return ($array[$offset]);
    }
    return $this->items[$offset] ?? null;
  }

  /**
   * @param mixed $offset
   * @param mixed $value
   *
   * @return void
   * @throws \ErrorException
   */
  public function offsetSet(mixed $offset, mixed $value): void {
    throw new \ErrorException('Cannot modify existing collection');
  }

  /**
   * @param mixed $offset
   *
   * @return void
   * @throws \ErrorException
   */
  public function offsetUnset(mixed $offset): void {
    throw new \ErrorException('Cannot modify existing collection');
  }

  /**
   * Modifies the set of items within a collection
   * @param callable|NULL $callback
   *
   * @return $this
   */
  public function map(callable $callback = null): static {
    $callback ??= static fn($x) => $x;
    $array = array_map($callback, $this->items);
    return new static($array);
  }

  /**
   * Performs an action for each item of a collection without modification
   * @param callable|NULL $callback
   *
   * @return $this
   */
  public function forEach(callable $callback = null): static {
    $callback ??= static fn($x, $y) => $x;
    foreach($this->items as $key => $item) {
      call_user_func($callback, $item, $key);
    }
    return $this;
  }

  /**
   * Limits the list of items within a collection
   * @param callable|NULL $callback
   *
   * @return $this
   */
  public function filter(callable $callback = null): static {
    $callback ??= static fn($x) => $x;
    $array = array_filter($this->items, $callback);
    return new static($array);
  }

  /**
   * Sorts the list of items within a collection according to a sort function
   * @param callable|null $callback
   *
   * @return $this
   */
  public function sort(callable $callback = null): static {
    $callback ??= static fn($a, $b) => $a <=> $b;
    $array = $this->items;
    if ($this->has_sequential_keys) {
      usort($array, $callback);
    }
    else {
      uasort($array, $callback);
    }
    return new static($array);
  }

  /**
   * Extract a subset of items from the Collection
   * @param string|int $start
   * @param int $amount
   *
   * @return $this
   */
  public function slice(string|int $start, int $amount): static {
    if (is_string($start)) {
      $start = $this->indexAt($start);
    }
    $array = array_slice($this->items, $start, $amount, !$this->has_sequential_keys);
    return new static($array);
  }

  /**
   * Reverses the array order within a collection
   * @return $this
   */
  public function reverse(): static {
    $array = array_reverse($this->items, !$this->has_sequential_keys);
    return new static($array);
  }

  /**
   * Removes the keys from a collection
   * @return $this
   */
  public function values(): static {
    $array = array_values($this->items);
    return new static($array);
  }

  /**
   * Limits the amount of items in the collection to the given number
   * @return $this
   */
  public function limit(int $amount): static {
    if ($this->count() < $amount) {
      return $this;
    }
    return $this->slice(0, $amount);
  }

  /**
   * Extracts data from a collection (simplified version of map)
   * @param string|callable $pluckable
   *
   * @return $this
   */
  public function pluck(string|callable $pluckable): static {
    // execute pluckable callback function to retrieve value from collection
    if (is_callable($pluckable)) {
      return $this->map($pluckable);
    }

    // throw warning and return existing collection
    if ($this->isScalarCollection()) {
      trigger_error('Invalid use of `pluck` on a collection of scalar values.', E_USER_WARNING);
      return $this;
    }

    // at this point, $pluckable is a string
    // determine appropriate callback function to retrieve value from collection
    $key = $pluckable;
    if ($this->isEntityCollection()) {
      $callback = fn(EntityDecoratorInterface $item) => $item->get($key);
    }
    elseif ($this->getType() === 'array') {
      $callback = fn(array $item) => (array_key_exists($key, $item) ? $item[$key] : null);
    }
    else {
      $callback = fn(object $item) => (property_exists($item, $key) ? $item->{$key} : NULL);
    }

    return $this->map($callback);
  }

  /**
   * Returns a collection of all the item's keys
   * @return $this
   */
  public function keys(): static {
    return new static($this->key_references);
  }

  /**
   * Returns the item at the specified key.
   * @param string|int $index
   *
   * @return mixed
   */
  public function index(string|int $index): mixed {
    // if looking for a numerical index on an associative array,
    // first check if the index exists, otherwise pass to offsetGet
    // to reference the key related to that specific numeric index
    if (!$this->has_sequential_keys && is_int($index)) {
      if (isset($this->items[$index])) {
        return $this->items[$index];
      }
    }
    return $this->offsetGet($index);
  }

  /**
   * Lookups the key for the given numerical index
   * @param int $index
   *
   * @return string|int
   */
  public function keyAt(int $index): string|int {
    if ($this->has_sequential_keys) {
      return $index;
    }

    $count = $this->count();
    return ($count > $index) ? $this->key_references[$index] : -1;
  }

  /**
   * Lookups the index for the given key
   * @param string|int $key
   *
   * @return int
   */
  public function indexAt(string|int $key): int {
    if ($this->has_sequential_keys) {
      return $key;
    }

    return array_search($key, $this->key_references);
  }

  /**
   * Counts the number of items contained
   * @return int
   */
  public function count(): int {
    return count($this->items);
  }

  /**
   * Returns an array of all the contained items
   * @return array
   */
  public function all(): array {
    return $this->items;
  }

  /**
   * Returns the first contained item in the list
   * @return mixed
   */
  public function first(): mixed {
    $array = $this->items;
    return reset($array) ?: null;
  }

  /**
   * Returns the final contained item in the list
   * @return mixed
   */
  public function last(): mixed {
    $array = $this->items;
    return end($array) ?: null;
  }

  /**
   * Reduces the items of a collection to a single value
   * @param callable|NULL $callback
   *
   * @return mixed
   */
  public function reduce(callable $callback = null): mixed {
    $callback ??= static fn($x, $y) => $y;
    $reduced_value = null;
    foreach($this->items as $item) {
      $reduced_value = call_user_func($callback, $item, $reduced_value);
    }
    return $reduced_value;
  }

  /**
   * Returns the type of uniformed collection
   * @return string
   */
  public function getType(): string {
    return $this->collection_type;
  }

  /**
   * Returns whether the collection consists of EntityDecorator items
   * @return bool
   */
  public function isEntityCollection(): bool {
    $type = $this->getType();
    return has_interface($type, EntityDecoratorInterface::class);
  }

  /**
   * Returns whether the collection consists of scalar value items
   * @return bool
   */
  public function isScalarCollection(): bool {
    $type = $this->getType();
    return in_array($type, ['string', 'int', 'double', 'boolean', 'NULL']);
  }

  /**
   * Returns whether the collection is empty of items
   * @return bool
   */
  public function isEmpty(): bool {
    return empty($this->items);
  }

  /**
   * Returns the mapped key at the specified position
   * @return string|int
   */
  protected function positionKey(): string|int {
    return $this->key_references[$this->position];
  }

  /**
   * @inheritDoc
   */
  public function current(): mixed {
    return $this[$this->position];
  }

  /**
   * @inheritDoc
   */
  public function next(): void {
    ++$this->position;
  }

  /**
   * @inheritDoc
   */
  public function key(): string|int {
    return $this->positionKey();
  }

  /**
   * @inheritDoc
   */
  public function valid(): bool {
    return isset($this[$this->position]);
  }

  /**
   * @inheritDoc
   */
  public function rewind(): void {
    $this->position = 0;
  }
}