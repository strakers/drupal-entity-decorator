<?php

namespace Drupal\entity_decorator\Decorators\Webform;

use Drupal\entity_decorator\Traits\IsUserOwned;

class WebformDecorator extends \Drupal\entity_decorator\Base\ContentEntityDecoratorBase {
  use IsUserOwned;

  public static function getClassOrModelName(): string {
    return 'Drupal\webform\Entity\Webform';
  }

  public function getTitle(): string {
    return $this->getFieldData('title', '');
  }

}