<?php

namespace Drupal\entity_decorator\Base;

use Drupal\entity_decorator\Traits\HasFormattedFields;

abstract class ContentEntityDecoratorBase extends EntityDecoratorBase implements ContentEntityDecoratorInterface {
  use HasFormattedFields; /* uses trait HasFields under the hood */
}