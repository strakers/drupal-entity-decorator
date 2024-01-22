<?php

namespace Drupal\entity_decorator_api\Support\Types;

use DateTime;
use DateTimeZone;
use DateTimeInterface;

class DateRange implements \Stringable {
  protected DateTimeInterface $start;
  protected DateTimeInterface $end;

  /**
   * @param \DateTimeInterface|string $start
   * @param \DateTimeInterface|string $end
   * @param \DateTimeZone|string $timezone
   */
  public function __construct(DateTimeInterface|string $start, DateTimeInterface|string $end, DateTimeZone|string $timezone = 'UTC') {
    $tz = $this->getTimeZoneFromString($timezone);
    $this->start = $this->getDateTimeFromString($start, $tz);
    $this->end = $this->getDateTimeFromString($end, $tz);
  }

  /**
   * Retrieve the start time of the range
   * @return \DateTimeInterface|null
   */
  public function getStart(): ?DateTimeInterface {
    return $this->start;
  }

  /**
   * Retrieve the end time of the range
   * @return \DateTimeInterface|null
   */
  public function getEnd(): ?DateTimeInterface {
    return $this->end;
  }

  /**
   * Determine if the given time (or today) is within the given date range
   * @param \DateTimeInterface|null $date
   *
   * @return bool
   */
  public function isWithinRange(?DateTimeInterface $date = null): bool {
    $date = $date ?? new DateTime();
    return $this->getStart() <= $date && $date < $this->getEnd();
  }

  /**
   * Determine if the start of the date range has passed.
   * @return bool
   */
  public function hasStarted(): bool {
    $date = new DateTime();
    return $this->getStart() <= $date;
  }

  /**
   * Determine if the end of the date range has passed.
   * @return bool
   */
  public function hasEnded(): bool {
    $date = new DateTime();
    return $date >= $this->getEnd();
  }

  /**
   * Utility for retrieving a timezone from a string or timezone format
   * @param \DateTimeZone|string $timezone
   *
   * @return \DateTimeZone
   */
  protected function getTimeZoneFromString(DateTimeZone|string $timezone = 'UTC'):DateTimeZone {
    return (is_a($timezone, DateTimeZone::class)
      ? $timezone
      : new DateTimeZone($timezone));
  }

  /**
   * Utility for retrieving a date from a string or datetime format
   * @param \DateTimeInterface|string $date
   * @param \DateTimeZone|string $timezone
   *
   * @return \DateTimeInterface|null
   */
  protected function getDateTimeFromString(DateTimeInterface|string $date, DateTimeZone|string $timezone = 'UTC'):?DateTimeInterface {
    $tz = $this->getTimeZoneFromString($timezone);
    try {
      return (is_a($date, DateTimeInterface::class)
        ? $date
        : new DateTime($date, $tz));
    }
    catch (\Exception $e) {
      \Drupal::logger('entity_decorator')
        ->error("Invalid DateRange string value: [{$date}]");
    }
    return null;
  }

  /**
   * Stringifies the date range
   * @return string
   */
  public function __toString(): string {
    $format = 'Y-m-d H:i:s';
    return sprintf(
      "%s  to  %s",
      $this->getStart()->format($format),
      $this->getEnd()->format($format)
    );
  }

}