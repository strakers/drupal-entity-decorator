<?php

namespace Drupal\webform_decorator;

use Drupal\entity_decorator_api\Traits\IsUserOwned;
use Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase;

/**
 * @method \Drupal\webform\Entity\Webform getEntity()
 */
class WebformDecorator extends ContentEntityDecoratorBase {
  use IsUserOwned;

  public static function getClassOrModelName(): string {
    return 'Drupal\webform\Entity\Webform';
  }

  public function getTitle(): string {
    return $this->getFieldData('title', '');
  }
}
