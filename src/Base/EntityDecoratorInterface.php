<?php

namespace Drupal\entity_decorator\Base;

use Drupal\Core\Entity\ContentEntityBase;

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
   * Exposes the entity for access to its methods and properties
   * @return \Drupal\Core\Entity\ContentEntityBase
   */
  public function getEntity(): ContentEntityBase;

  /**
   * Update an entity's data field value
   * @param string $field_name
   * @param $value
   *
   * @return void
   */
  public function setFieldData(string $field_name, $value): void;

  /**
   * Retrieve an entity's data field value
   * @param string $field_name
   * @param $fallback
   *
   * @return ?
   */
  public function getFieldData(string $field_name, $fallback = null);

  /**
   * Retrieve an array of all an entity's data fields and their values
   * @return array
   */
  public function getAllFieldData(): array;

  /**
   * List an array of all an entity's data field keys
   * @return array
   */
  public function listAllFieldNames(): array;

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
}