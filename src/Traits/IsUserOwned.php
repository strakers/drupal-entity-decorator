<?php

namespace Drupal\entity_decorator\Traits;

use Drupal\user\UserInterface;
use Drupal\entity_decorator\Support\Utility\has_trait;

trait IsUserOwned {

  /**
   * Retrieves the user account that "owns" this entity
   *
   * @return \Drupal\user\UserInterface
   * @throws \Exception
   */
  public function getOwner(): UserInterface {
    $class = static::getEntityClassName();
    if (!has_trait($class,'Drupal\user\EntityOwnerTrait')) {
      throw new \Exception("Class does not use the EntityOwnerTrait");
    }
    return $this->getEntity()->getOwner();
  }

  /**
   * Load entity accessors for all entities own by a given user
   * @param string|int|\Drupal\user\UserInterface $owner
   *
   * @return array
   */
  public static function loadAllOwned(string|int|UserInterface $owner): array {
    $user_field_key = 'uid';
    $static_class = static::class;

    // first check if load by properties static method exists
    if (! method_exists($static_class, 'loadByProperties')) return [];

    // then check if other field is defined to reference the owner/user
    $class_vars = get_class_vars($static_class);
    if (array_key_exists('user_field', $class_vars)) {
      $user_field_key = $class_vars['user_field'];
    }

    // pull the user id, regardless if a string, int, or user object is passed
    $owner_id = $owner instanceof UserInterface
      ? $owner->id()
      : $owner;

    // get items
    return static::loadByProperties([
      $user_field_key => $owner_id,
    ]);
  }
}