<?php

namespace Drupal\entity_decorator\Traits;

trait HasFields {

  /**
   * Update an entity's data field value
   * @param string $field_name
   * @param $value
   *
   * @return void
   */
  public function set(string $field_name, $value): void {
    $this->getEntity()->set($field_name, $value);
  }

  /**
   * Retrieve an entity's data field value
   * @param string $field_name
   * @param $fallback
   *
   * @return mixed
   */
  public function get(string $field_name, $fallback = null) {
    $field = $this->getEntity()->get($field_name);

    // if field returns raw value
    if (!is_object($field)) {
      return ($field ?: $fallback);
    }

    // otherwise extract value
    $value_array = $field->getValue();

    // if empty, use fallback value instead
    if (empty($value_array)) return ($fallback);

    $data_value = null;

    // when one item in array
    if (count($value_array) === 1) {
      if (isset($value_array[0])) {
        $keyed_array = $value_array[0];

        // if no value, return fallback
        if (empty($keyed_array))
          $data_value = $fallback;

        // return value item if singular array or entire array if multiple
        else
          $data_value = ((count($keyed_array) === 1) ? reset($keyed_array) : $keyed_array);
      }
      else {
        // return list of values
        $data_value = ($value_array);
      }
    }

    // when value holds multiple values
    else {
      $values = [];
      foreach($value_array as $value_item) {
        if (empty($value_item)) continue;
        if (count($value_item) === 1) {
          $values[] = array_values($value_item)[0];
        }
        else {
          $values[] = $value_item;
        }
      }
      $data_value = ($values ?: $fallback);
    }

    return ($data_value);
  }

  /**
   * Retrieve an array of all an entity's data fields and their values
   * @return array
   */
  public function getAll(): array {
    $data = [];
    $values = $this->getEntity()->toArray();
    foreach($values as $key => $value) {
      $data[$key] = $this->getFieldData($key);
    }
    return $data;
  }

  /**
   * List an array of all an entity's data field keys
   * @return array
   */
  public function listAllFieldNames(): array {
    return array_keys($this->getEntity()->toArray());
  }
}