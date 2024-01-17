<?php

namespace Drupal\entity_decorator\Support\DataType;

use Drupal\entity_decorator\Base\EntityDecoratorBase;

class Collection implements \ArrayAccess, \Countable, \Iterator {
  public readonly array $items;
  protected readonly array $key_references;
  protected readonly bool $has_sequential_keys;
  protected int $position = 0;

  public function __construct(array $items, bool $preserve_keys = true) {
    $this->items = $preserve_keys ? $items : array_values($items);
    $this->key_references = array_keys($items);
    $this->has_sequential_keys = array_is_list($this->items);
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
   * Reverses the array order within a collection
   * @return $this
   */
  public function reverse(): static {
    $array = array_reverse($this->items, !$this->has_sequential_keys);
    return new static($array);
  }

  /**
   * Returns the item at the specified key.
   * @param string|int $index
   *
   * @return \Drupal\entity_decorator\Base\EntityDecoratorBase|null
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
   * Returns an array of all the item's keys
   * @return array
   */
  public function keys(): array {
    return $this->key_references;
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
   * @return \Drupal\entity_decorator\Base\EntityDecoratorBase|null
   */
  public function first(): mixed {
    $array = $this->items;
    return reset($array);
  }

  /**
   * Returns the final contained item in the list
   * @return \Drupal\entity_decorator\Base\EntityDecoratorBase|null
   */
  public function last(): mixed {
    $array = $this->items;
    return end($array);
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