<?php

namespace Drupal\entity_decorator\Base;

interface ContentEntityDecoratorInterface extends EntityDecoratorInterface {

  /**
   * Update an entity's data field value with storage format (if defined)
   * @param string $field_name
   * @param $value
   *
   * @return void
   */
  public function set(string $field_name, $value): void;

  /**
   * Retrieve an entity's data field value with access format (if defined)
   * @param string $field_name
   * @param $fallback
   *
   * @return ?
   */
  public function get(string $field_name, $fallback = null);

  /**
   * Retrieve an array of all an entity's data fields and their values with access formats (if defined)
   * @return array
   */
  public function getAll(): array;

  /**
   * List an array of all an entity's data field keys
   * @return array
   */
  public function listAllFieldNames(): array;

  /**
   * Update an entity's data field value as-is
   * @param string $field_name
   * @param $value
   *
   * @return void
   */
  public function setRawData(string $field_name, $value): void;

  /**
   * Retrieve an entity's data field value as-is
   * @param string $field_name
   * @param $fallback
   *
   * @return mixed
   */
  public function getRawData(string $field_name, $fallback);

  /**
   * Retrieve an array of all an entity's data fields and their values as-is
   * @return array
   */
  public function getAllRawData(): array;

}