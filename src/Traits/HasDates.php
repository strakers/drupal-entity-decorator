<?php

namespace Drupal\entity_decorator_api\Traits;

use DateTime;
use DateTimeInterface;
use Drupal\entity_decorator_api\Support\Types\DateRange;

trait HasDates {

  /**
   * Retrieve date range for given period
   *
   * @param string $field_name
   *
   * @return \Drupal\entity_decorator_api\Support\Types\DateRange|null
   */
  public function getDates(string $field_name): ?DateRange {
    $dates = $this->getFieldData($field_name);
    if ($dates && isset($data['value']) && isset($data['end_value'])) {
      return new DateRange($dates['value'], $dates['end_value']);
    }
    return null;
  }

  /**
   * Retrieve date time range for given field string
   * @param string $field_name
   *
   * @return \DateTimeInterface|null
   * @throws \Exception
   */
  public function getDate(string $field_name): ?DateTimeInterface {
    $date = $this->getFieldData($field_name);
    return $date ? new DateTime("@{$date}") : null;
  }

}