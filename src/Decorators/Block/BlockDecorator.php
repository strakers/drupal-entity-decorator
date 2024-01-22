<?php

namespace Drupal\entity_decorator_api\Decorators\Block;

/**
 * @method Drupal\block\Entity\Block getEntity()
 */
class BlockDecorator extends \Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase {

  /**
   * @inheritDoc
   */
  public static function getClassOrModelName(): string {
    return 'Drupal\block\Entity\Block';
  }

}