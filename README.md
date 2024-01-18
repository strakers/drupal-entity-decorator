# Drupal Entity Decorator API

Implements a decorator pattern to access and customize an entity's properties and methods, with the option to strongly
type return values.

---

## Installation

```bash
composer require strakez/drupal-entity-decorator
```

---

## Requirements

- PHP 8.1+
- Drupal 10+

---

## Purpose

Provides a simple means to retrieve and display entity data, and solves some of the challenges experienced when working
with entities. This implementations of the decorator pattern wraps the entity and provides customized methods for
interacting with the entity.

Please note, this only exposes classes for use in other modules and does not provide any Drupal functionality on its own.
There will not be anything to display via the UI unless specifically implemented.

---

## Usage

```php
$id = 1;
$node = NodeDecorator::load($id); // Collection

$node->id(); // 1
$node->get('title'); // 'My First Node'
$node->get('sticky'); // false
```

For information on using and extending decorators, see the [Decorator documentation](docs/Decorators.md).

---

## Working with Collections

When loading multiple decorators at a time, the data will be representated in a collection. This provides an advantage
over simple arrays, as this data can be easily sorted, mutated, counted, and indexed.

```php
$set = NodeDecorator::loadByOwned($user); // Collection
```

For more information, see the [Collection documentation](docs/Collections.md).

## Final Words

Please have fun using this API, and feel free to submit your comments and/or improvements if you find any. Thanks!
