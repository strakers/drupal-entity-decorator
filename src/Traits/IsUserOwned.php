<?php

namespace Drupal\entity_decorator\Traits;

use Drupal\Core\Session\AccountInterface;
use Drupal\entity_decorator\Support\DataType\Collection;
use function Drupal\entity_decorator\Support\Utility\has_trait;

trait IsUserOwned {

  /**
   * Retrieves the user account that "owns" this entity
   *
   * @return AccountInterface
   * @throws \Exception
   */
  public function getOwner(): AccountInterface {
    $class = static::getEntityClassName();
    if (!has_trait($class,'Drupal\user\EntityOwnerTrait')) {
      throw new \Exception("Class does not use the EntityOwnerTrait");
    }
    return $this->getEntity()->getOwner();
  }

  /**
   * Load entity accessors for all entities own by a given user
   * @param string|int|AccountInterface $owner
   *
   * @return Collection
   */
  public static function loadAllOwned(string|int|AccountInterface $owner = 0): Collection {
    $user_field_key = 'uid';
    $static_class = static::class;

    if (!$owner) {
      $owner = \Drupal::currentUser();
    }

    // first check if load by properties static method exists
    if (! method_exists($static_class, 'loadByProperties')) return new Collection([]);

    // then check if other field is defined to reference the owner/user
    $class_vars = get_class_vars($static_class);
    if (array_key_exists('user_field', $class_vars)) {
      $user_field_key = $class_vars['user_field'];
    }

    // pull the user id, regardless if a string, int, or user object is passed
    $owner_id = $owner instanceof AccountInterface
      ? $owner->id()
      : $owner;

    // get items
    return static::loadByProperties([
      $user_field_key => $owner_id,
    ]);
  }
}