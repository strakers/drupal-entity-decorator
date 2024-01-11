<?php

namespace Drupal\entity_decorator\Traits;

use Drupal\entity_decorator\Support\Conversion\CastAs;

trait HasFormattedFields {
  use HasFields {
    set as public setRawData;
    get as public getRawData;
    getAll as public getAllRawData;
  }

  /**
   * Shorthand to set/update a field's data with option to convert to storage format
   * @param string $field_name
   * @param $value
   *
   * @return void
   */
  public function set(string $field_name, $value): void {
    if (!is_null($value)) {
      $callback = $this->getStorageFormatter($field_name);
      $value = $callback($value);
    }

    $this->setRawData($field_name, $value);
  }

  /**
   * Retrieve an entity's data field value as formatted value
   * @param string $field_name
   * @param $fallback
   *
   * @return mixed|null
   */
  public function get(string $field_name, $fallback = null) {
    $value = $this->getRawData($field_name, /* null */);
    if (is_null($value)) {
      return $fallback;
    }
    $callback = $this->getAccessFormatter($field_name);
    return $callback($value);
  }

  /**
   * Retrieve an array of all an entity's data fields and their formatted values
   * @return array
   */
  public function getAll(): array {
    $data = [];
    $values = $this->getAllRawData();
    foreach($values as $key => $value) {
      $callable = $this->getAccessFormatter($key);
      $data[$key] = $callable($value);
    }
    return $data;
  }

  public function getAccessFormatter(string $field_name): callable|string {
    $formatters = $this->getAccessFormatters();
    return $this->getFormatterFromArray($field_name, $formatters);
  }

  public function getStorageFormatter(string $field_name): callable|string {
    $formatters = $this->getStorageFormatters();
    return $this->getFormatterFromArray($field_name, $formatters);
  }

  public function getAccessFormatters(): array {
    return [];
  }

  public function getStorageFormatters(): array {
    return [];
  }

  public function getFormatterFromArray(string $field_name, array $formatters): callable|string {
    if (array_key_exists($field_name, $formatters)) {
      $formatter = $formatters[$field_name];

      // check if closure function
      if ($formatter instanceof \Closure) {
        return $formatter;
      }

      // extract callable function from string/method
      return CastAs::bind($formatter);
    }

    // use "noop" function to return the value as-is
    return CastAs::existingType();
  }
}