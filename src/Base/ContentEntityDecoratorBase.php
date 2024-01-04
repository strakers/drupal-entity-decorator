<?php

namespace Drupal\entity_decorator\Base;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\entity_decorator\Traits\HasFields;

abstract class ContentEntityDecoratorBase extends EntityDecoratorBase implements ContentEntityDecoratorInterface {
  use HasFields;

  /**
   * Expose the raw entity methods and properties
   * @return ContentEntityBase
   */
  public function getEntity(): ContentEntityBase {
    /**
     * Just to quiet the editor's return validation checks from screaming
     * @var ContentEntityBase $entity
     */
    $entity = $this->entity;
    return $entity;
  }

}