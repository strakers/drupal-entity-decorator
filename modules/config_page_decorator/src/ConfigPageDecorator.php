<?php

namespace Drupal\config_page_decorator;

use Drupal\entity_decorator\Base\ContentEntityDecoratorBase;
use Drupal\entity_decorator\Traits\HasSingleInstance;
use Drupal\Core\Entity\EntityBase;
use DateTime;

class ConfigPageDecorator extends ContentEntityDecoratorBase {
  use HasSingleInstance;

  /**
   * Prevents public access to constructor method
   *
   * @param \Drupal\Core\Entity\EntityBase $entity
   */
  protected function __construct(EntityBase $entity) {
    parent::__construct($entity);
  }

  /**
   * @inheritDoc
   */
  protected static function getClassOrModelName(): string {
    return 'Drupal\config_pages\Entity\ConfigPages';
  }

  /**
   * Retrives the label/title of the config page
   * @return string
   */
  public function label(): string {
    return $this->getFieldData('label');
  }

  /**
   * Retrieves the datetime of last update made to the config page
   * @return DateTime|null
   */
  public function getLastUpdated(): ?DateTime {
    $changed = $this->getFieldData('changed');
    return $changed ? new DateTime("@{$changed}") : null;
  }

}