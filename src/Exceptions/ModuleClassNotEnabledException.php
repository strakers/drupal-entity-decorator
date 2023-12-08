<?php

namespace Drupal\entity_decorator\Exceptions;

class ModuleClassNotEnabledException extends \Exception implements \Drupal\Component\Plugin\Exception\ExceptionInterface {
  public function __construct(string $class, string $module, \Throwable $previous = null)
  {
    parent::__construct(sprintf('Class "%s" not found. Please check that the %s has been enabled', $class, $module), 0, $previous);
  }
}