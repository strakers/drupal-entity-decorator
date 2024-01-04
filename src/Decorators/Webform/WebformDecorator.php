<?php

namespace Drupal\entity_decorator\Decorators\Webform;

class WebformDecorator extends \Drupal\entity_decorator\Base\ContentEntityDecoratorBase {

  protected static function getClassOrModelName(): string {
    return 'Drupal\webform\Entity\Webform';
  }

}