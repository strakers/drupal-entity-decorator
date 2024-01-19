<?php

namespace Drupal\entity_decorator_api\Base;

use Drupal\entity_decorator_api\Traits\HasFormattedFields;

abstract class ContentEntityDecoratorBase extends EntityDecoratorBase implements ContentEntityDecoratorInterface {
  use HasFormattedFields; /* uses trait HasFields under the hood */
}