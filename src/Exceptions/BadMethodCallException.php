<?php

namespace Drupal\entity_decorator_api\Exceptions;

use Drupal\Component\Plugin\Exception\ExceptionInterface;

class BadMethodCallException extends \Exception implements ExceptionInterface {
  public function __construct(string $class, string $module, \Throwable $previous = null)
  {
    parent::__construct(sprintf('Method %s does not belong to class %s.', $class, $module), 0, $previous);
  }
}