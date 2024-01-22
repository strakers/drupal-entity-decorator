<?php

namespace Drupal\entity_decorator_api\Decorators\Block;

/**
 * @method Drupal\block_content\Entity\BlockContent getEntity()
 */
class BlockContentDecorator extends \Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase {

  /**
   * @inheritDoc
   */
  public static function getClassOrModelName(): string {
    return 'Drupal\block_content\Entity\BlockContent';
  }

}