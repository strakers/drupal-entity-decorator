# Collections

Collections improve on arrays by providing chainable methods for altering and mutating datasets. They are also intended 
to be of uniform typing throughout the collection, so if one item is an EntityDecorator, all items should be 
EntityDecorators.

See below for a reference to Collection methods. The upcoming Collection examples will reference the collection below:
```php
$array = [
  'foo' => 9, 
  'bar' => 1999,
  'baz' => 30, 
];

$collection = new Collection($array);
```

## Accessing items

Collections implement the ArrayAccess interface, allowing them to use array notation to access key-values, on top of the 
various methods available.
```php
$collection['foo']; // 9
$collection['bar']; // 1999
$collection['baz']; // 30
```

Due to some internal magic, Collections can also access associative arrays numerically, as if they were sequential 
arrays. This is performed by keeping an internal array registry of the order of keys.
```php
$collection[0]; // 9
$collection[1]; // 1999
$collection[2]; // 30
```

### Special interaction

When using Collections that use a mix of string and numerical keys, accessing the Collection using a numerical key may 
yield an unexpected result. In this scenario, please see below for the expected behavior:
```php
$mixedKeyCollection = new Collection([
  'foo' => 12, 
  'bar' => 578,
  99, 7, 39506, // implicitly using numeric keys
  'baz' => 101, 
]);
 
// sequential access yields values in order, instead of implicitly set numeric keys
$mixedKeyCollection[0]; // 12
$mixedKeyCollection[1]; // 578
$mixedKeyCollection[2]; // 99
$mixedKeyCollection[3]; // 7
```
In order to access the item with the value of `99`, use a string representation of the numeric key, or use the `index` 
method instead. This is because associative arrays convert implicitly set numeric keys to their string representations.
```php
// for comparison
$mixedKeyCollection[0]; // 12

// intended target
$mixedKeyCollection['0']; // 99
$mixedKeyCollection->index(0); // 99
```

Finally, please note, this behavior also applies on associative arrays when keys are explicitly set using integers:
```php
$mixedKeyCollection2 = new Collection([
  'foo' => 12, 
  'bar' => 578,
  0 => 99, 
  1 => 7, 
  2 => 39506,
  'baz' => 101, 
]);
 
// sequential access
$mixedKeyCollection2[0]; // 12
$mixedKeyCollection2[1]; // 578
$mixedKeyCollection2[2]; // 99
$mixedKeyCollection2[3]; // 7

// direct access
$mixedKeyCollection2['0']; // 99
$mixedKeyCollection2->index(0); // 99
```

## Loops
Collection implement the Iterator interface allowing traversal in the same manner as arrays. `Foreach` loops are 
recommended, however, both `for` and `while` loops are also usable.
```php
// standard foreach loop
foreach($collection as $key => $value) {
  echo $key . ' -- ' . $value;
}
// output
// -------------
# foo -- 9
# bar -- 1999
# baz -- 30
```

```php
// for loop, while looking up the key for the current position
for($i = 0; $i < $collection->count(); $i++) {
  echo $i . ' -- ' . $collection->keyAt($i) . '--' . $collection[$i];
}
// output
// -------------
# 0 -- foo -- 9
# 1 -- bar -- 1999
# 2 -- baz -- 30
```

```php
// while loop, while looking up the key for the current position
$i = 0;
$count = $collection->count();
while($i < $count) {
  echo $i . ' -- ' . $collection->keyAt($i) . '--' . $collection[$i];
  $i++;
}
// output
// -------------
# 0 -- foo -- 9
# 1 -- bar -- 1999
# 2 -- baz -- 30
```

## Available Methods

### Executive Methods
- *all(): array*
    - returns an array of the items in the collection
- *count(): int*
    - returns the number of items in the collection
- *first(): mixed*
    - returns the first item from the list
- *index($key): mixed*
    - returns the item at the given index. For associative arrays, `key` can be a string. If `key` is an
      integer in this scenario, they key at the given index is used.
      ```php
      $collection->index('foo'); // 9
      $collection->index(1); // undefined
    
      // for contrast, using array notation...
      $collection['foo']; // 9
      
      // this works due to the difference between how index and array notation operate
      $collection[1]; // 9
        ```
- *indexAt($index): string | int*
    - returns the numerical index for the given key. Primarily used for associative or hybrid keyed collections
- *keyAt($index): string | int*
    - returns the key at the given index. Primarily used for associative or hybrid keyed collections
- *last(): mixed*
    - returns the final item in the list
- *reduce($callback): mixed*
    - compiles the items in a collection to a single value

### Chainable Methods
Chainable methods return Collection instances, allowing (like the name suggests) chaining them together to mutate the 
dataset. Each method returns a new collection, preserving the original dataset for possible later use.
- *filter($callback): Collection*
    - limits the items of the Collection to a set of conditions
      ```php
      $newCollection = $collection->filter(static fn($n) => $n / 3 > 5);
      $newCollection->all(); // [ 'bar' => 1999, 'baz' => 30 ]
      ```
- *forEach($callback): Collection*
  - performs actions on items of the collection without mutating them
    ```php
    $products = [];
    $newCollection = $collection->forEach(static fn($n) => $products[] = $n * 5);
    $newCollection->all(); // [ 'foo' => 9, 'bar' => 1999, 'baz' => 30 ]
    $products; // [ 45, 9995, 150 ]
    ```
- *map($callback): Collection*
    - mutates each item of the Collection
      ```php
      $newCollection = $collection->map(static fn($n) => $n * 0.1);
      $newCollection->all(); // [ 'foo' => 0.9, 'bar' => 199.9, 'baz' => 3.0 ]
      ```
- *pluck($pluckable): Collection*
    - retrieves a single field from each item in the collection
      ```php
      $personCollection = new Collection([
        'john' => ['name' => 'John Smith', 'age' => 23],
        'ann' => ['name' => 'Ann Rogers', 'age' => 49],
        'miyu' => ['name' => 'Miyu Yamamoto', 'age' => 18],
      ]);
      $newCollection = $personCollection->pluck('age');
      $newCollection->all(); // [ 'john' => 23, 'ann' => 49, 'miyu' => 18 ]
      ```
- *reverse(): Collection*
    - reverses the order of the items in the Collection. If items is associative, keys are preserved
      ```php
      $newCollection = $collection->reverse();
      $newCollection->all(); // [ 'bar' => 1999, 'baz' => 30, 'foo' => 9 ]
      ```
- *slice($start, $amount): Collection*
    - extracts given amount of items from the Collection from the given start point. For associative arrays, the start
    point can be a string key
      ```php
      $newCollection = $collection->slice(2, 2);
      $newCollection->all(); // [ 'baz' => 30, 'bar' => 1999 ]
        
      // also, using string key
  
      $newCollection = $collection->slice('baz', 2);
      $newCollection->all(); // [ 'baz' => 30, 'bar' => 1999 ]
      ```
- *sort($callback): Collection*
    - sorts the items in the Collection according to a given logic
      ```php
      $newCollection = $collection->sort(static fn($a, $b) => $n / 3 > 5);
      $newCollection->all(); // [ 'foo' => 9, 'baz' => 30, 'bar' => 1999 ]
      ```

### Other Methods
- *getType(): string*
    - returns the type of each item in the collection
- *isEmpty(): bool*
    - checks if a collection is empty
- *isEntityCollection(): bool*
    - checks if each item in the collection is an Entity
- *isScalarCollection(): bool*
    - checks if each item in the collection is a scalar value (int, float, string, bool, or null)
- *keys(): array*
    - returns a list of keys in the collection

&nbsp;