<?php

namespace Drupal\entity_decorator_api\Decorators\Webform;

use Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase;
use Drupal\entity_decorator_api\Traits\IsUserOwned;

class WebformDecorator extends ContentEntityDecoratorBase {
  use IsUserOwned;

  public static function getClassOrModelName(): string {
    return 'Drupal\webform\Entity\Webform';
  }

  public function getTitle(): string {
    return $this->getFieldData('title', '');
  }

}