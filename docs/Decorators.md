# Using Decorators

This module comes packed with a few basic decorators to begin. The base classes can always be extended to include
additional functionality.

## Loading a Decorator Instance

```php
use Drupal\node\Entity\Node;
use Drupal\entity_decorator_api\Decorators\Node\NodeDecorator;

// load entity decorator by id
$node = NodeDecorator::load(1);
$node->id(); // integer
$node->label(); // string
$node->getOwner(); // AccountInterface

// decorate an existing entity
$raw_node = Node::load(2);
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
  $node->set('sticky', 0);
}
```

## Extending Base Classes
When extending classes, make sure to return the Fully-Qualified-Class-Name (FQCN) of the entity to decorate:
```php
class BlockDecorator extends EntityDecoratorBase {
  public static function getClassOrModelName(): string {
    return 'Drupal\block\Entity\Block';
  }
}

class UserDecorator extends ContentEntityDecoratorBase {
  public static function getClassOrModelName(): string {
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
class BlockDecorator extends EntityDecoratorBase {
  // for tracking created/modified timestamps, and managing field access
  use HasDates, HasFormattedFields;
}
```


## Entity Ownership

```php
use Drupal\webform_decorator\WebformSubmissionDecorator;

// collect a list of UUIDs from all owned webforms
// when no argument is passed, passes the current user
$submissions = WebformSubmissionDecorator::loadAllOwned();
foreach($submissions as $submission) {
  $uuids[] = $submission->uuid();
}
var_dump($uuids);

// display the owner name
$submission = WebformSubmissionDecorator::load($id);
echo $submission->getOwner()->getDisplayName();
```

Classes with ownership can implement the `IsUserOwned` trait.


## Working with Fields

```php
$submission->get('remote_addr', '127.0.0.1'); // include fallback value if field/value not found
$submission->getAll(); // export all data to array
$submission->listAllFieldNames(); // export array of field names/keys
$submission->set('notes','Hello World'); // update field value
```

Classes that use the `HasFormatters` trait or extend the `ContentEntityDecoratorBase` class also have the following
methods available.

```php
$submission->getRawData('remote_addr', '127.0.0.1'); // include fallback value if field/value not found
$submission->getAllRawData(); // export all data to array
$submission->setRawData('notes','Hello World'); // update field value
```
These differ from their above counterparts by always returning unformatted data if access or storage
formatters are defined. See below for more information on access and storage formatters.


## Casting Field Formats
### Implicit Accessor Formatting

Normally, field data will be retrieved as it is stored. For timestamps, this is usually a string value, and IDs can
often display as strings. Decorators shine by allowing classes to automatically cast a value to a particular format
when predefined.

Take a demo entity with the following field values (and data types):
```php
class DemoEntityDecorator extends ContentEntityDecoratorBase {}

// load arbitrary demo entity instance
$demo = DemoEntityDecorator::load($id);

$demo->get('field_1'); // 1 (integer)
$demo->get('field_2'); // "hello world" (string)
$demo->get('field_3'); // "2024-01-04 09:23:11" (date string)
```

If we predefine the access formats as below...
```php
class DemoEntityDecorator extends ContentEntityDecoratorBase {
  public function getAccessorFormats() {
    // includes accessor formats defined in a parent class
    return parent::getAccessorFormats() + [
       'field_1' => 'string',
       'field_2' => 'dateTime',
       'field_3' => fn($x) => strtoupper($x),
    ];
  }
}
```

... then when accessed, fields will display the following:
```php
$demo->get('field_1'); // "1" (string)
$demo->get('field_2')); // "HELLO WORLD" (string)
$demo->get('field_3'); // object (DateTime)

// also, since field_3 is a DateTimeObject,
// we can directly use its methods!
$demo->get('field_3')->format('F jS, Y'); // "January 4th, 2024" (string)
```
&nbsp;

### Alternative Accessor Formatting

For those that prefer to strongly type the return values, it may be preferable to define custom methods over casting.
```php
class DemoEntityDecorator extends ContentEntityDecoratorBase {
  public function getField1(): string {
    return (string) $this->getRawData('field_1');
  }
  public function getField2(): string {
    $value = $this->getRawData('field_2');
    return strtoupper($value);
  }
  public function getField3(): \DateTime {
    $value = $this->getRawData('field_3')
    return new DateTime($value);
  }
}
```

However, please note that implicit formatting can also be strongly typed by using private/protected methods:
```php
class DemoEntityDecorator extends ContentEntityDecoratorBase {
  public function getAccessorFormats() {
    // includes accessor formats defined in a parent class
    return parent::getAccessorFormats() + [
       'field_3' => $this->formatToUpperCase(),
       'field_4' => $this->formatToLowerCase(),
       'field_5' => $this->formatCommasToArray(),
    ];
  }
  
  // converts text to uppercase
  protected function formatToUpperCase(): callable {
    return function(string $x): string {
      return strtoupper($x);
    }
  }
  
  // converts text to lowercase
  protected function formatToLowerCase(): callable {
    return function(string $x): string {
      return strtoupper($x);
    }
  }
  
  // converts comma separated string to array
  // can even use shorthand method
  protected function formatCommasToArray(): callable {
    return fn(string $x): array => explode(',', $x);
  }
}
```


### Implicit Storage Formatting

In a similar fashion as when accessing data, storing data also be formatted. A more practical example of this usage may
involve casting to and from DateTime formats. The following example casts a field value from string to DateTime when
accessing the value, and back to string for storage.

```php
class DemoEntityDecorator extends ContentEntityDecoratorBase {
  const DATE_FORMAT = 'Y-m-d H:i:s';

  public function getAccessorFormats() {
    return parent::getAccessorFormats() + [
       'date_field' => fn(string $x) => DateTime::createFromFormat(self::DATE_FORMAT, $x),
    ];
  }

  public function getStorageFormats() {
    // includes storage formats defined in a parent class
    return parent::getStorageFormats() + [
       'date_field' => fn(DateTime $x) => $x->format(self::DATE_FORMAT),
    ];
  }
}

// initiate demo instance
$demo2 = DemoEntityDecorator::load($id);

// retrieves DateTime object
$date = $demo2->get('date_field'); 
echo $date->format('F jS, Y'); // "January 4th, 2024"

// add a day to the date
$date->add(DateInterval::createFromDateString('1 day'));
echo $date->format('F jS, Y'); // "January 5th, 2024"

// stores value as string
$demo2->set('date_field', $date); 
```


### Alternative Storage Formatting

Again, as an alternative to implicit casting for storage formatting, defining custom methods for this purpose can
perform the same feat.

```php
class DemoEntityDecorator extends ContentEntityDecoratorBase {
  const DATE_FORMAT = 'Y-m-d H:i:s';
  
  public function getDate(): DateTime {
    $value = $this->getRawData('date_field');
    return DateTime::createFromFormat(self::DATE_FORMAT, $x);
  }
  
  public function setDate(DateTime $value): void {
    $new_value = $value->format(self::DATE_FORMAT);
    $this->setRawData('date_field', $new_value);
  }
}
```
&nbsp;