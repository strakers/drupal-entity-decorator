# Drupal Entity Decorator
Implements a decorator pattern to access and customize an entity's properties and methods, with the option to strongly 
type return values.

## Installation

```bash
composer require strakez/drupal-entity-decorator
```

## Requirements

- PHP 8.1+
- Drupal 10+

## Purpose

Provides a simple means to retrieve and display entity data, and solves some of the challenges experienced when working 
with entities. This implementations of the decorator pattern wraps the entity and provides customized methods for 
interacting with the entity.

Please note, this only exposes classes for use in other modules and does not provide any Drupal functionality on its own. 
There will not be anything to display via the UI unless specifically implemented.

## Usage

This module comes packed with a few basic decorators to begin. The base classes can always be extended to include 
additional functionality.

### Loading a Decorator Instance

```php
use Drupal\entity_decorator\Decorators\Node\NodeDecorator;
use Drupal\node\Entity\Node;

// load entity decorator by id
$node = NodeDecorator::load(1);
$node->getId(); // integer
$node->getLabel(); // string
$node->getOwner(); // AccountInterface

// decorate an existing entity
$raw_node = Node::load(3);
$node2 = NodeDecorator::decorate($raw_node);

// another way to load a single entity decorator - this time by uuid:
$node3 = NodeDecorator::loadByUuid('erueer-253453-efeffe-344y78');

// this loads only the first item found
$node4 = NodeDecorator::loadOneByProperties([
  'title' => 'Hello World',
]);
```
### Loading Multiple Decorator Instances

```php
$nodes = NodeDecorator::loadByProperties([
  'sticky' => 1,
  'status' => 1,
]);

foreach($nodes as $node) {
  $node->setFieldData('sticky', 0);
}
```

### Entity Ownership

```php
use Drupal\entity_decorator\Decorators\Webform\WebformSubmissionDecorator;

// collect a list of UUIDs from all owned webforms
// when no argument is passed, passes the current user
$submissions = WebformSubmissionDecorator::loadAllOwned();
foreach($submissions as $submission) {
  $uuids[] = $submission->getUuid();
}
var_dump($uuids);

// display the owner name
$submission = WebformSubmissionDecorator::load(3);
echo $submission->getOwner()->getDisplayName();
```

Classes with ownership can implement the `IsUserOwned` trait.

### Working with Fields

```php
$submission->getFieldData('remote_addr', '127.0.0.1'); // include fallback value if field/value not found
$submission->getAllFieldData(); // export all data to array
$submission->listAllFieldNames(); // export array of field names/keys
$submission->setFieldData('notes','Hello World'); // update field value
```

Classes can be extended to add more specific field retrieval methods, and more tightly typed data representations.

```php
class CommentDecorator extends ContentEntityDecorator {
  public function getPermalink(): Url {
    return $this->getFieldData('permalink');
  }
}
```

### Extending Base Classes
When extending classes, make sure to return the Fully-Qualified-Class-Name (FQCN) of the entity to decorate:
```php
class BlockDecorator extends EntityDecorator {
  protected static function getClassOrModelName(): string {
    return 'Drupal\block\Entity\Block';
  }
}

class UserDecorator extends ContentEntityDecorator {
  protected static function getClassOrModelName(): string {
    return 'Drupal\user\Entity\User';
  }
}

// inherits the `getClassOrModelName` method from parent, since articles are just "special" nodes
class ArticleDecorator extends NodeDecorator {
  // add decorator methods here...
}
```

There are existing traits available via `src/Traits` for reusable functionality. You can also create your own 
traits to extend the existing functionality.
```php
class BlockDecorator extends EntityDecorator {
  // for tracking created/modified timestamps, and managing field access
  use HasDates, HasFields;
}
```
