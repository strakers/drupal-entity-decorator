<?php

namespace Drupal\entity_decorator\Decorators\Taxonomy;

use Drupal\entity_decorator\Exceptions\ModuleClassNotEnabledException;
use Drupal\entity_decorator\Exceptions\BadMethodCallException;

class TaxonomyTermDecorator extends \Drupal\entity_decorator\Base\ContentEntityDecoratorBase {

  protected static string $entity_type_id = 'taxonomy_term';

  /**
   * @inheritDoc
   */
  protected static function getClassOrModelName(): string {
    return 'Drupal\taxonomy\Entity\Term';
  }

  /**
   * @inheritDoc
   */
  public static function loadByProperties(array $props, array $defaults = ['status' => 1]): array {
    return parent::loadByProperties($props, $defaults);
  }

  /**
   * @inheritDoc
   */
  public static function loadOneByProperties(array $props, array $defaults = ['status' => 1]): ?static {
    return parent::loadOneByProperties($props, $defaults);
  }

  /**
   * Determines whether the term has any parent terms
   * @return bool
   */
  public function hasParent(): bool {
    return (int)$this->getFieldData('parent') !== 0;
  }

  /**
   * Determines whether the term has been marked as enabled or not
   * @return bool
   */
  public function isEnabled(): bool {
    return ((int) $this->getFieldData('status')) > 0;
  }

  /**
   * Retrives the parent taxonomy term accessor in the tree
   * Todo: Handle terms with multiple parents. Accepting PRs :)
   *
   * @return $this|null
   * @throws ModuleClassNotEnabledException
   * @throws BadMethodCallException
   */
  public function getParent(): ?static {
    if (($parent_id = (int)$this->getFieldData('parent')) && $parent_id > 0) {
      return static::load($parent_id);
    }
    return null;
  }

  /**
   * Retrieves immediate child taxonomy term accessors from the tree.
   * Option for loading disabled terms as well.
   *
   * @param bool $includeInactive
   *
   * @return array
   */
  public function getChildren(bool $includeInactive = false): array {
    $statusOption = $includeInactive ? [] : [ 'status' => 1 ];
    return static::loadByProperties([
        'parent' => $this->getId(),
      ] + $statusOption);
  }

  /**
   * Provides an array of labels of each immediate child term
   *
   * @param bool $includeInactive
   *
   * @return array
   */
  public function getChildLabels(bool $includeInactive = false): array {
    return array_map(fn(self $term) => $term->getLabel(), $this->getChildren($includeInactive));
  }

  /**
   * Retrieves the description of the taxonomy term, if set
   *
   * @return string
   */
  public function getDescription(): string {
    return ($data = $this->getFieldData('description'))
      ? $data['value'] ?? ''
      : '';
  }

  /**
   * Returns the label of a taxonomy term, given its `tid`
   *
   * @param int|string $id
   *
   * @static
   * @return string
   * @throws ModuleClassNotEnabledException
   * @throws BadMethodCallException
   */
  public static function getLabelById(int|string $id): string {
    return ($term = static::load($id)) ? $term->getLabel() : '';
  }

  /**
   * Returns the id of a taxonomy term, given its label
   *
   * @param string $label
   *
   * @static
   * @return int
   */
  public static function getIdByLabel(string $label): int {
    return ($term = static::loadOneByProperties([
      'name' => $label,
    ])) ? $term->getId() : 0;
  }

  /**
   * Retrives an array of taxonomy tree objects for a given taxonomy.
   * Option for flat or nested structures
   *
   * @param string $taxonomy_name
   * @param bool $flatten_structure
   *
   * @static
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTree(string $taxonomy_name = '', bool $flatten_structure = true): array {
    if (!$taxonomy_name) return [];
    $entity_type_id = static::$entity_type_id ?? '';
    $tree = \Drupal::entityTypeManager()
      ->getStorage($entity_type_id)
      ->loadTree($taxonomy_name);

    // for flattened structure, use the default output
    if ($flatten_structure) return $tree;

    // for non-flattened structure, build out tree
    $processed = [];
    $current_level = 0;
    $tree_with_depth = [];

    // items can appear in any order, so in order to properly map a child to its
    // parent, we run iterate over the items twice in a nested foreach loop,
    // marking which has been processed, and moving to the next unprocessed item
    foreach($tree as $branch) {
      if (array_key_exists($branch->tid, $processed)) continue;
      foreach($tree as $x_branch) {
        if ((int)$x_branch->depth === $current_level) {
          if ($current_level === 0) {
            $tree_with_depth[] = $x_branch;
          }
          else {
            // children can have multiple parents of different branches, so we
            // loop through each parent and assign the child to that parent. If
            // one of the parents has yet to be processed, we break this
            // iteration and move to the next item, saving this one for later
            foreach($x_branch->parents as $parent_id) {
              if (!array_key_exists($parent_id, $processed)) continue 2;
              $parent = $processed[$parent_id];
              $parent->children ??= [];
              if (!in_array($x_branch, $parent->children)) {
                $parent->children[] = $x_branch;
              }
            }
          }
          $processed[$x_branch->tid] = $branch;
        }
      }
      $current_level++;
    }
    // it's negligible, but emptying the processed variable for memory savings.
    // it's like saving a penny off the purchase of a house... still savings :)
    unset($processed);

    return $tree_with_depth;
  }

}