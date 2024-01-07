<?php

namespace Drupal\entity_decorator\Base;

use Drupal\Core\Entity\EntityBase;
use Drupal\entity_decorator\Traits\HasFields;

abstract class ContentEntityDecoratorBase extends EntityDecoratorBase implements ContentEntityDecoratorInterface {
  use HasFields;

//  /**
//   * Expose the raw entity methods and properties
//   * @return EntityBase
//   */
//  public function getEntity(): EntityBase {
//    $entity = $this->entity;
//    return $entity;
//  }

}