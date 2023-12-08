<?php

namespace Drupal\entity_decorator\Traits;

use Drupal\user\UserInterface;

trait IsUserOwned {

  /**
   * Retrieves the user account that "owns" this entity
   * @return \Drupal\user\UserInterface
   */
  public function getOwner(): UserInterface {
    return $this->getEntity()->getOwner();
  }

  /**
   * Load entity accessors for all entities own by a given user
   * @param string|int|\Drupal\user\UserInterface $owner
   *
   * @return array
   */
  public static function loadAllOwned(string|int|UserInterface $owner): array {
    $user_field = 'uid';
    if (! method_exists(static::class, 'loadByProperties')) return [];
    if (property_exists(static::class, '$user_field')) {
      $user_field = static::$user_field;
    }

    $owner_id = $owner instanceof UserInterface::class
      ? $owner->id()
      : $owner;
    return static::loadByProperties([
      static::$user_field => $owner_id,
    ]);
  }
}