<?php

namespace Drupal\entity_decorator\Decorators\User;

class UserDecorator extends \Drupal\entity_decorator\Base\ContentEntityDecoratorBase {

  protected static function getClassOrModelName(): string {
    return 'Drupal\user\Entity\User';
  }

  public function getRoles(): string {
    return $this->getEntity()->getRoles();
  }

  public function getUsername(): string {
    return $this->getFieldData('name');
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

  public function casts(): array {
    // TODO: Implement casts() method.
    return [];
  }

}