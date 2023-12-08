<?php

namespace Drupal\entity_decorator\Base;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\entity_decorator\Traits\HasDataFields;
use Drupal\entity_decorator\Traits\CanBeLoadedByProperties;
use Drupal\entity_decorator\Exceptions\ModuleClassNotEnabledException;
use Drupal\entity_decorator\Exceptions\BadMethodCallException;
use Psr\Log\LoggerInterface;

abstract class EntityDecoratorBase implements EntityDecoratorInterface {
  use CanBeLoadedByProperties, HasDataFields;

  protected static LoggerInterface $logger;

  public function __construct(protected readonly ContentEntityBase $entity) {
    static::$logger = \Drupal::logger('entity_decorator');
  }

  /**
   * Expose the raw entity methods and properties
   * @return ContentEntityBase
   */
  public function getEntity(): ContentEntityBase {
    return $this->entity;
  }

  /**
   * @param int|string $id The ID of the entity instance to load
   * @param string $class The fully qualified class name of the entity
   * @param string $method The method name to initiate an instance of the entity
   * @param string|null $module The name of the module containung the class
   *
   * @return mixed
   * @throws ModuleClassNotEnabledException
   * @throws BadMethodCallException
   */
  protected static function getEntityInstanceFromClassName(int|string $id, string $class, string $method = 'load', ?string $module = null): mixed {
    // attempts to determine if the entity name is being passed instead of a class name
    // if so, and entity exists, retrieves the class name for the entity
    if (!preg_match('/([\\-\s\/]+?)/', $class)) {
      try {
        $class = \Drupal::entityTypeManager()
          ->getDefinition($class)
          ->getOriginalClass();
      }
      catch(\Exception $e) {}
    }

    // if the module name is not passed, retrieves the name from the class path
    if (!$module) {
      preg_match('/\\\\?Drupal\\\\([a-z_]+?)\\\\/i', $class, $matches);
      $module = end($matches);
    }

    if (! class_exists($class))
      throw new ModuleClassNotEnabledException($class, $module);

    if (! method_exists($class, $method))
      throw new BadMethodCallException($class, $module);

    return $class::$method($id);
  }

}