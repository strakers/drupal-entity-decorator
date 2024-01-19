<?php

namespace Drupal\entity_decorator_api\Base;

use Drupal\Core\Entity\EntityBase;
use Drupal\entity_decorator_api\Traits\CanBeLoadedByProperties;
use Drupal\entity_decorator_api\Exceptions\ModuleClassNotEnabledException;
use Drupal\entity_decorator_api\Exceptions\BadMethodCallException;
use Psr\Log\LoggerInterface;
use Drupal;

use function Drupal\entity_decorator_api\Support\Utility\toSnakeCase;

abstract class EntityDecoratorBase implements EntityDecoratorInterface {
  use CanBeLoadedByProperties;

  protected static LoggerInterface $logger;
  protected readonly int|string $id;

  public function __construct(protected readonly EntityBase $entity) {
    static::$logger = Drupal::logger('entity_decorator');
    $this->id = $this->entity->id();
  }

  /**
   * Expose the raw entity methods and properties
   * @return EntityBase
   */
  public function getEntity(): EntityBase {
    return $this->entity;
  }

  /**
   * @inheritDoc
   */
  public function id(): int|string {
    return $this->getEntity()->id();
  }

  /**
   * @inheritDoc
   */
  public function uuid(): string {
    return $this->getEntity()->uuid();
  }

  /**
   * @inheritDoc
   */
  public function bundle(): string {
    return $this->getEntity()->bundle();
  }

  /**
   * @inheritDoc
   */
  public function label(): string {
    return $this->getEntity()->label();
  }

  /**
   * @inheritDoc
   */
  public function save() {
    return $this->getEntity()->save();
  }


  /**
   * @inheritDoc
   */
  public static function load(int|string $id): ?static {
    $entity = static::getEntityInstance($id);
    if ($entity) {
      return static::decorate($entity);
    }
    return null;
  }

  /**
   * @inheritDoc
   */
  public static function decorate(EntityBase $entity): static {
    $desiredClass = static::getEntityClassName();
    $class = $entity::class;
    if (!is_a($entity, $desiredClass)) {
      throw new \ValueError("Given entity must be of type '{$desiredClass}', but '{$class}' was given.");
    }
    return new static($entity);
  }

  /**
   * @param int|string $id The ID of the entity instance to load
   * @param string $method The method name to initiate an instance of the entity
   * @param string|null $module The name of the module containing the class
   *
   * @return mixed
   * @throws ModuleClassNotEnabledException
   * @throws BadMethodCallException
   */
  protected static function getEntityInstance(int|string $id, string $method = 'load', ?string $module = null): mixed {
    // attempts to determine if the entity name is being passed instead of a class name
    // if so, and entity exists, retrieves the class name for the entity
    $class = static::getEntityClassName();

    // if the module name is not passed, retrieves the name from the class path
    if (!$module) {
      preg_match('/\\\\?Drupal\\\\([a-z_]+?)\\\\/i', $class, $matches);
      $module =  end($matches);
    }

    if (! class_exists($class))
      throw new ModuleClassNotEnabledException($class, $module);

    if (! method_exists($class, $method))
      throw new BadMethodCallException($class, $module);

    return $class::$method($id);
  }

  /**
   * Determines/retrieves the entity class name of the current decorator
   * @return string
   */
  protected static function getEntityClassName(): string {
    $class = $classOrEntityName = static::getClassOrModelName();
    if (!preg_match('/([\\-\s\/]+?)/', $classOrEntityName)) {
      try {
        $class = Drupal::entityTypeManager()
          ->getDefinition($classOrEntityName)
          ->getOriginalClass();
      }
      catch(\Exception $e) {}
    }
    return $class;
  }

  /**
   * Returns either the model name or the fully qualified class name of the entity
   * @return string
   */
  abstract public static function getClassOrModelName(): string;

  /**
   * Retrieve entity type name from defined class path of the entity
   * @param string|null $class
   *
   * @return string
   */
  protected static function getEntityTypeFromClassName(?string $class = null): string {
    $class ??= static::getEntityClassName(); // toSnakeCase
    preg_match('/\\\\([a-z_]+?)$/i', $class, $matches);
    return toSnakeCase(end($matches));
  }

  /**
   * @param string $module_name
   *
   * @return mixed
   */
  static protected function logger(string $module_name = 'entity_decorator'):LoggerInterface {
    return static::$logger ?? (static::$logger = Drupal::logger($module_name));
  }
}