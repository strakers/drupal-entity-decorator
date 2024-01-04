<?php

namespace Drupal\entity_decorator\Decorators\Media;

class MediaDecorator extends \Drupal\entity_decorator\Base\ContentEntityDecoratorBase {

  /**
   * @inheritDoc
   */
  protected static function getClassOrModelName(): string {
    return 'Drupal\media\Entity\Media';
  }

}