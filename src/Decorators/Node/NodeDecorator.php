<?php

namespace Drupal\entity_decorator_api\Decorators\Node;

use Drupal\entity_decorator_api\Base\ContentEntityDecoratorBase;
use Drupal\entity_decorator_api\Traits\IsUserOwned;
use Drupal\entity_decorator_api\Traits\HasTimestamps;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;

class NodeDecorator extends ContentEntityDecoratorBase {
  use IsUserOwned, HasTimestamps;

  protected static LanguageInterface $language_manager;

  public static function getClassOrModelName(): string {
    return 'Drupal\node\Entity\Node';
  }

  /**
   * @return \Drupal\Core\Language\LanguageInterface
   */
  protected static function getLanguageManager(): LanguageInterface {
    return static::$language_manager ?? (static::$language_manager = \Drupal::languageManager()->getCurrentLanguage());
  }

  /**
   * Retrieves the label/title of the given node
   * @return string
   */
  public function label(): string {
    return $this->getRawData('title');
  }

  /**
   * Retrieves the bundle type of the given node
   * @return string
   */
  public function type(): string {
    return $this->bundle();
  }

  /**
   * @param bool $use_absolute_path
   *
   * @return \Drupal\Core\Url
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function getUrl(bool $use_absolute_path = false): Url {
    return $this->getEntity()->toUrl([
      'absolute' => $use_absolute_path,
      'language' => static::getLanguageManager(),
    ]);
  }

  /**
   * @param bool $use_absolute_path
   *
   * @return string
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function getInternalPath(bool $use_absolute_path = false): string {
    return $this->getUrl($use_absolute_path)->getInternalPath();
  }

  /**
   * @param bool $use_absolute_path
   *
   * @return string
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function getAliasedPath(bool $use_absolute_path = false): string {
    return $this->getUrl($use_absolute_path)->toString();
  }

  /**
   * Determines whether the node has been marked as enabled or not
   * @return bool
   */
  public function isEnabled(): bool {
    return ((int) $this->getRawData('status')) > 0;
  }

  /**
   * Determines whether the node has been marked as promoted or not
   * @return bool
   */
  public function isPromoted(): bool {
    return ((int) $this->getRawData('promote')) > 0;
  }

  /**
   * Determines whether the node has been marked as sticky or not
   * @return bool
   */
  public function isSticky(): bool {
    return ((int) $this->getRawData('sticky')) > 0;
  }

  public function getAccessFormatters(): array {
    return [
      'sticky' => 'boolean',
      'promote' => 'boolean',
      'status' => 'boolean',
    ];
  }

}