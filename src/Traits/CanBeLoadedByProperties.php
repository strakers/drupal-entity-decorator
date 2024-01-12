<?php

namespace Drupal\entity_decorator\Traits;

use Ramsey\Collection\Collection;

trait CanBeLoadedByProperties {

  /**
   * Dynamically load a list of an entity type by a given set of properties
   * @param string $entity_type_id
   * @param array $props
   *
   * @return array
   */
  protected static function getEntitiesByProperties(string $entity_type_id, array $props): array {
    $logger = \Drupal::logger('nms_utility');
    try {
      return \Drupal::entityTypeManager()
        ->getStorage($entity_type_id)
        ->loadByProperties($props);
    }
    catch(\Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException $e) {
      static::logger()->warning(sprintf('The entity type "%s" is an invalid plugin definition', $entity_type_id));
    }
    catch(\Drupal\Component\Plugin\Exception\PluginNotFoundException $e) {
      static::logger()->warning(sprintf('The entity type "%s" not found', $entity_type_id));
    }
    return [];
  }

  /**
   * Load a list of accessors for entities that match the given properties
   *
   * @param array $props
   * @param array $defaults
   *
   * @return \Ramsey\Collection
   */
  public static function loadByProperties(array $props, array $defaults = []): Collection {
    $set = [];
    $entity_type_id = static::$entity_type_id ?? static::getEntityTypeFromClassName() ?? '';
    $results = static::getEntitiesByProperties($entity_type_id, $props + $defaults);

    foreach($results as $key => $entity) {
      if ($entity) {
        $set[$key] = new static($entity);
      }
    }

    return new Collection(
      static::getEntityTypeFromClassName(),
      $set,
    );
  }

  /**
   * Load one (or less) accessor for an entity that matches the given properties
   *
   * @param array $props
   * @param array $defaults
   *
   * @return static|null
   */
  public static function loadOneByProperties(array $props, array $defaults = []): ?static {
    $entity_type_id = static::$entity_type_id ?? '';
    $results = static::getEntitiesByProperties($entity_type_id, $props + $defaults);
    return !empty($results) ? new static(array_values($results)[0]) : null;
  }

  /**
   * Load an accessor for an entity that matches the given uuid
   *
   * @param string $uuid
   *
   * @return static|null
   */
  public static function loadByUuid(string $uuid): ?static {
    $uuid_field = 'uuid';
    return static::loadOneByProperties([
      $uuid_field => $uuid,
    ]);
  }
}