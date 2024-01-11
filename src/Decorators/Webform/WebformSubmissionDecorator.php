<?php

namespace Drupal\entity_decorator\Decorators\Webform;

use Drupal\entity_decorator\Traits\IsUserOwned;
use Drupal\entity_decorator\Traits\HasTimestamps;

class WebformSubmissionDecorator extends \Drupal\entity_decorator\Base\ContentEntityDecoratorBase {
  use IsUserOwned, HasTimestamps;

  /**
   * @inheritDoc
   */
  protected static function getClassOrModelName(): string {
    return 'Drupal\webform\Entity\WebformSubmission';
  }

  /**
   * @inheritDoc
   */
  public function setRawData(string $field_name, $value): void {
    // if (array_key_exists($field_name, $this->listAllWebformFieldNames()))
      $this->getEntity()->setElementData($field_name, $value);
  }

  /**
   * @inheritDoc
   */
  public function getFieldData(string $field_name, $fallback = null) {
    $entity = $this->getEntity();

    // search for field in base entity fields
    $value = null;
    try { $value = $this->getBaseFieldData($field_name); }
    catch(\Exception $e){}

    // if not found, search through form elements
    if (is_null($value)) {
      $value = $entity->getElementData($field_name);
    }

    return $value ?? $fallback;
  }

  /**
   * Retrieve the base entity date field values
   * @param string $field_name
   * @param $fallback
   *
   * @return array|mixed|null
   */
  public function getBaseFieldData(string $field_name, $fallback = NULL) {
    return parent::getRawData($field_name, $fallback);
  }

  /**
   * Retrieve a list of all submitted webform field and their values
   * @return array
   */
  public function getAllWebformFieldData(): array {
    return $this->getEntity()->getData();
  }

  /**
   * Retrieve a list of all field values related to the webform submission
   * @return array
   */
  public function getAllRawData(): array {
    return $this->getAllWebformFieldData() + parent::getAllRawData();
  }

  /**
   * Retrieve a list of all field data keys
   * @return array
   */
  public function listAllFieldNames(): array {
    return [
      ...parent::listAllFieldNames(),
      ...$this->listAllWebformFieldNames(),
    ];
  }

  /**
   * Retrieve a list of all webform-specfic field data keys
   * @return array
   */
  protected function listAllWebformFieldNames(): array {
    return array_keys($this->getEntity()->getData());
  }

  /**
   * Retrieve the current workflow status for a submission
   * Todo: make workflow field modular (in case it is named differently)
   *
   * @param string $workflow_field_name
   *
   * @return string
   */
  public function getWorkflowStatus(string $workflow_field_name = 'workflow'): string {
    $workflow = $this->getFieldData($workflow_field_name);
    if (isset($workflow['workflow_state'])) {
      return $workflow['workflow_state'];
    }
    return '';
  }

  /**
   * Determine if the webform_submission is currently in draft mode
   * @return bool
   */
  public function isDraft(): bool {
    return (bool) $this->getBaseFieldData('in_draft');
  }

  /**
   * Determine whether the webform_submission has been completed
   * @return bool
   */
  public function isCompleted(): bool {
    return (bool) $this->getBaseFieldData('completed');
  }

  /**
   * Determine whether the webform_submission has been locked from further edits
   * @return bool
   */
  public function isLocked(): bool {
    return (bool) $this->getBaseFieldData('locked');
  }

  /**
   * Determine whether the webform_submission has been marked by an admin
   * @return bool
   */
  public function isStarred(): bool {
    return (bool) $this->getBaseFieldData('sticky');
  }

  public function key(): string {
    return $this->getFieldData('id', '');
  }

  public function getAccessFormatters(): array {
    // TODO: Implement casts() method.
    return [
      'completed' => 'boolean',
      'in_draft' => 'boolean',
      'locked' => 'boolean',
      'sticky' => 'boolean',
    ];
  }

}