<?php

namespace Drupal\entity_decorator\Decorators\Media;

use Drupal\entity_decorator\Traits\IsUserOwned;
use Drupal\entity_decorator\Traits\HasTimestamps;

class MediaDecorator extends \Drupal\entity_decorator\Base\ContentEntityDecoratorBase {
  use IsUserOwned, HasTimestamps;

  /**
   * @inheritDoc
   */
  protected static function getClassOrModelName(): string {
    return 'Drupal\media\Entity\Media';
  }

}