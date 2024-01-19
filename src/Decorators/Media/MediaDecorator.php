<?php

namespace Drupal\entity_decorator_api\Decorators\Media;

use Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase;
use Drupal\entity_decorator_api\Traits\IsUserOwned;
use Drupal\entity_decorator_api\Traits\HasTimestamps;

class MediaDecorator extends ContentEntityDecoratorBase {
  use IsUserOwned, HasTimestamps;

  /**
   * @inheritDoc
   */
  public static function getClassOrModelName(): string {
    return 'Drupal\media\Entity\Media';
  }

}