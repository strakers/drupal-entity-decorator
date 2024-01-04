<?php

namespace Drupal\entity_decorator\Traits;

use DateTime;

/**
 * @method mixed getFieldData
 */
trait HasTimestamps {

  /**
   * Retrieves the timestamp at which the entity was created
   *
   * @return DateTime|null
   * @throws \Exception
   */
  public function getCreatedTime(): ?DateTime {
    $time = $this->getFieldData('created');
    return new DateTime($time);
  }

  /**
   * Retrieves the timestamp at which the entity was last modified
   *
   * @return DateTime|null
   * @throws \Exception
   */
  public function getModifiedTime(): ?DateTime {
    $time = $this->getFieldData('changed');
    return new DateTime($time);
  }
}