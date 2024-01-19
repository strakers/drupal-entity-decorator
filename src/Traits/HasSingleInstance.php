<?php

namespace Drupal\entity_decorator_api\Traits;

use Drupal\entity_decorator_api\Exceptions\BadMethodCallException;
use Drupal\entity_decorator_api\Exceptions\ModuleClassNotEnabledException;

trait HasSingleInstance {

  /**
   * @var array
   */
  protected static array $_instances = [];

  /**
   * Retrieves a singleton instance of the specified entity
   *
   * @param string $id
   *
   * @return static|null
   * @throws ModuleClassNotEnabledException
   * @throws BadMethodCallException
   */
  protected static function getInstance(string $id): ?static {
    if (empty(static::$_instances) || !isset(static::$_instances[$id])) {
      $instance = static::getEntityInstance($id);
      if ($instance) {
        static::$_instances[$id] = new static($instance);
      }
    }
    return static::$_instances[$id] ?? null;
  }

  /**
   * Creates a singleton accessor for the given entity identified by $id
   * @param int|string $id
   *
   * @return static|null
   * @throws ModuleClassNotEnabledException
   * @throws BadMethodCallException
   */
  public static function load(int|string $id): ?static {
    return static::getInstance($id);
  }

}