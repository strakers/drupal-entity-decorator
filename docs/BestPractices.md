# Tips & Best Practices

## Table of Contents
* [01](#check-for-existence-after-loading-decorator): Check for existence after loading decorator
* [02](#alias-decorator-class-name-for-easier-access): Alias decorator class name for easier access
* [03](#add-phpdoc-return-value-for-getentity-method-when-extending-classes): Add PHPDoc return value for getEntity method when extending classes

## Check for existence after loading decorator

When loading a single decorator, it's recommended to first check for its existence immediately afterward in order to 
ensure an item was loaded. This holds true for any of the following methods for loading a single decorator:
```php
// using load method
$item = MyEntityDecorator::load($id);

// using loadOneByProperties method
$item = MyEntityDecorator::loadOneByProperties(['type' => $type]);

// getting a single item from an entity collection
// using: $collection = MyEntityDecorator::loadAllOwned();
$item = $collection->first(); // also: last(), index(), etc...
```

Since these methods return a null value if the item was not found, wrapping them in an if statement (or following up 
with one) ensures your code will not break in the event the item is not found.
```php
if ($item = MyEntityDecorator::load($id)) {
  // ... safely encapsulated code here ...
}

// or, using an early negative return/break/die

$item = MyEntityDecorator::load($id);
if (!$item) die('Painfully, but still gracefully');
// .. safe code continues past here ...
```

Please note that when using the array notation, one should also first check to ensure the index exists.
```php
$collection = MyEntityDecorator::loadAllOwned();
if (isset($collection[0])) {
  // ... safely encapsulated code here ...
}

// or, using length check

if($collection->count() > 3) {
  // can now use any of the following:
  // $collection[0], $collection[1], or $collection[3]
}
```

This check is not required for `for` and `foreach` loops, granted the bounds are set in the loop definition.
```php
for($i = 0; $i < $collection->count(); $i++) {
  // ... can safely use $collection[$i] ...
}
```
```php
for($i = 0; $i < 7; $i++) {
  // collection bounds do not necessarily match loop definition
  // existence checks are required for this type of usage
  if (isset($collection[$i])) {
    // ... safe ...
  }
}
```
Lastly, depending on the usage, ternary and null coalescing operators may be suitable:
```php
$node = NodeDecorator::load($id) ?? $backupNodeDecorator;

// or

$node = $nodeCollection->isEmpty() 
   ? $backupNodeDecorator 
   : $nodeCollection[0];
```

## Alias decorator class name for easier access

Decorator class names typically add the word "Decorator" to the entity name to distinguish them and their purpose. 
However, this can create some extra long class names that can be a pain to type when developing.
```php
class MyReallyLongNamedClassForBlockEntityAccessDecorator extends ContentEntityDecoratorBase {
  // ... class definition ...
}
```
As a result of this, it is normal to alias the class name upon import to make it easier to work with:
```php
use MyReallyLongNamedClassForBlockEntityAccessDecorator as MyDecorator;
$item = MyDecorator::load($id);
```
For some cases, it may even be helpful to alias class names (despite not being long) in order to telegraph their function:
```php
use UserDecorator as User;
use NodeDecorator as Node;
$user = User::loadOneByProperties(['name' => 'foobar']);
$nodes = Node::loadAllOwned($user ?? 0); // loads the Entity Decorator instance for the given node
```

## Add PHPDoc return value for getEntity method when extending classes

Currently, when extending the base decorator classes, one specifies the entity type via the `getClassOrModelName` 
method. This informs the internal magic of the class what entity to work with. Unfortunately, we're not yet able to 
dynamically communicate this to IDEs when developing. 

The current solution is to add a class-level PHPDoc comment to 
specify the entity return type. Unfortunately, this means writing the FQCN of the entity twice in the same class 
(sorry OOP-police!), but it provides a simple solution until dynamic typing is possible (or until I learn about it!).
```php
/**
* @var \Drupal\fake_entity\Entity\FakeEntity getEntity()
 */
class FakeEntityDecorator extends ContentEntityDecoratorBase {
  public static function getClassOrModelName(): string {
    return 'Drupal\fake_entity\Entity\FakeEntity';
  }
  // ... continue defining methods for class ...
}
```
&nbsp;