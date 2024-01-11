<?php

namespace Drupal\entity_decorator\Base;

use Drupal\Core\Entity\EntityBase;

interface ContentEntityDecoratorInterface extends EntityDecoratorInterface {

  /**
   * Exposes the entity for access to its methods and properties
   * @return \Drupal\Core\Entity\EntityBase
   */
  public function getEntity(): EntityBase;

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

  public function casts(): array;
  public function getFormat(string $field_name): callable|string;
  public function get(string $field_name, $fallback);
  public function set(string $field_name, $value): void;

}