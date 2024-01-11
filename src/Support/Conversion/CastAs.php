<?php

namespace Drupal\entity_decorator\Support\Conversion;

use \DateTime;
use \DateTimeZone;
use \DateTimeInterface;
use Drupal\entity_decorator\Support\Date\DateRange;

class CastAs {
  public static function custom(mixed $value, callable $fn): mixed {
    if (is_callable($fn)) {
      return call_user_func($fn, $value);
    }
    return $value;
  }

  public static function type(mixed $value, string $type): mixed {
    if (method_exists(static::class, $type)) {
      $fn = static::class . "::$type";
      return $fn($value);
    }
    return $value;
  }

  public static function bind(string $type): callable|string {
    if (method_exists(static::class, $type)) {
      $fn = static::class . "::$type";
      if (is_callable($fn))
        return $fn;
    }
    return static::existingType();
  }

  public static function existingType(): callable {
    return (fn($x) => $x);
  }

  public static function string(mixed $value): string {
    return match(true) {
      is_scalar($value),
        is_array($value),
        method_exists($value,'__toString') => (string) $value,
      method_exists($value,'toString') => $value->toString(),
      default => $value,
    };
  }

  public static function integer(mixed $value): int {
    return is_numeric($value) ? (int)$value : NAN;
  }

  public static function float(mixed $value): float {
    return is_numeric($value) ? (float)$value : NAN;
  }

  public static function boolean(mixed $value): bool {
    return (bool) $value;
  }

  public static function array(mixed $value): array {
    return (is_array($value) ? $value : [$value]);
  }

  public static function object(mixed $value): object {
    if (is_array($value)) return (object) $value;
    return is_object($value) ? $value : ((object) ['value'=>$value]);
  }

  public static function dateTime(mixed $value): ?DateTime {
    $tz = new DateTimeZone('UTC');
    try {
      return (is_a($value, DateTimeInterface::class)
        ? $value
        : new DateTime($value, $tz));
    }
    catch (\Exception $e) {
      // retry with value modification hack
      try { return new DateTime("@{$value}", $tz); }
      catch (\Exception $e2) {}
    }
    return null;
  }

  public static function dateRange(mixed $value): ?DateRange {
    if (is_array($value) && array_key_exists('value', $value)  && array_key_exists('end_value', $value)) {
      ['value' => $start, 'end_value' => $end] = $value;
      return new DateRange($start, $end);
    }
    return null;
  }
}