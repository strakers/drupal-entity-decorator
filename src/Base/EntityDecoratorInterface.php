<?php

namespace Drupal\entity_decorator\Base;

use Drupal\Core\Entity\EntityBase;

interface EntityDecoratorInterface {
  /**
   * Retrieves the ID reference of the entity
   * @return int|string
   */
  public function getId(): int|string;

  /**
   * Retrieves the UUID reference of the entity
   * @return string
   */
  public function getUuid(): string;

  /**
   * Retrieves the bundle/entity type of the entity
   * @return int|string
   */
  public function getBundle(): int|string;

  /**
   * Retrieves the label/title of the entity
   * @return string
   */
  public function getLabel(): string;

  /**
   * Exposes the entity for access to its methods and properties
   * @return \Drupal\Core\Entity\EntityBase
   */
  public function getEntity(): EntityBase;

  /**
   * Create an accessor instance for the entity identified by $id
   * @param string|int $id
   *
   * @static
   * @return static|null
   */
  public static function load(string|int $id): ?static;

  /**
   * Load a list of accessors for entities that match the given properties
   *
   * @param array $props
   * @param array $defaults
   *
   * @return array
   */
  public static function loadByProperties(array $props, array $defaults = []): array;

  /**
   * Load one (or less) accessor for an entity that matches the given properties
   *
   * @param array $props
   * @param array $defaults
   *
   * @return static|null
   */
  public static function loadOneByProperties(array $props, array $defaults = []): ?static;

  /**
   * Load an accessor for an entity that matches the given uuid
   * @param string $uuid
   *
   * @return static|null
   */
  public static function loadByUuid(string $uuid): ?static;

  /**
   * Decorate an existing entity
   * @param \Drupal\Core\Entity\EntityBase $entity
   *
   * @return static
   */
  public static function decorate(EntityBase $entity): static;
}