<?php

namespace Drupal\entity_decorator_api\Decorators\User;

use Drupal\Core\Session\AccountInterface;
use Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase;

/**
 * @method \Drupal\user\Entity\User getEntity()
 */
class UserDecorator extends ContentEntityDecoratorBase implements AccountInterface {

  public static function getClassOrModelName(): string {
    return 'Drupal\user\Entity\User';
  }

  public function getRoles($exclude_locked_roles = false): array {
    return $this->getEntity()->getRoles();
  }

  /**
   * Get the user name of the user account. Alias of getAccountName()
   *
   * @return string
   */
  public function getUsername(): string {
    return $this->getRawData('name');
  }

  /**
   * @inheritDoc
   */
  public function hasRole(int|string $rid): bool {
    return $this->getEntity()->hasRole($rid);
  }

  /**
   * @inheritDoc
   */
  public function hasPermission($permission): bool {
    return $this->getEntity()->hasPermission((string)$permission);
  }

  /**
   * @inheritDoc
   */
  public function isActive():bool {
    return $this->getEntity()->isActive();
  }

  /**
   * @inheritDoc
   */
  public function isAuthenticated(): bool {
    return $this->getEntity()->isAuthenticated();
  }

  /**
   * @inheritDoc
   */
  public function isAnonymous(): bool {
    return $this->getEntity()->isAnonymous();
  }

  /**
   * @inheritDoc
   */
  public function getAccountName(): string {
    return $this->getEntity()->getAccountName();
  }

  /**
   * @inheritDoc
   */
  public function getDisplayName(): \Drupal\Component\Render\MarkupInterface|string {
    return $this->getEntity()->getDisplayName();
  }

  /**
   * @inheritDoc
   */
  public function getEmail(): ?string {
    return $this->getEntity()->getEmail();
  }

  /**
   * @inheritDoc
   */
  public function getTimeZone(): string {
    return $this->getEntity()->getTimeZone();
  }

  /**
   * @inheritDoc
   */
  public function getLastAccessedTime(): int {
    return $this->getEntity()->getLastAccessedTime();
  }

  /**
   * @inheritDoc
   */
  public function getPreferredLangcode($fallback_to_default = true): string {
    return $this->getEntity()->getPreferredLangcode((bool)$fallback_to_default);
  }

  /**
   * @inheritDoc
   */
  public function getPreferredAdminLangcode($fallback_to_default = true): string {
    return $this->getEntity()->getPreferredAdminLangcode((bool)$fallback_to_default);
  }

}