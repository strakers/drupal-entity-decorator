<?php

namespace Drupal\entity_decorator_api\Decorators\User;

use Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase;

/**
 * @method \Drupal\user\Entity\User getEntity()
 */
class UserDecorator extends ContentEntityDecoratorBase {

  public static function getClassOrModelName(): string {
    return 'Drupal\user\Entity\User';
  }

  public function getRoles(): string {
    return $this->getEntity()->getRoles();
  }

  public function getUsername(): string {
    return $this->getRawData('name');
  }

  public function hasRole(int|string $rid): bool {
    return $this->getEntity()->hasRole($rid);
  }

  public function hasPermission(string $permission): bool {
    return $this->getEntity()->hasPermission($permission);
  }

  public function isActive():bool {
    return $this->getEntity()->isActive();
  }

}