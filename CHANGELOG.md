# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

* Decorators for Block and BlockContent entities.
* Documentation file for covering usage best practices.

### Changed

* Collection methods `first()` and `last()` now return `null` instead of `false` when no value is found.
* Migrated `loadByProperties()` and related methods from trait to `EntityDecoratorBase` class.
* `UserDecorator` class now implements `Drupal\Core\Session\AccountInterface` to better satisfy usage requirements. Can 
now function as a drop-in replacement for User and Account classes (for decorator purposes).

## Deprecated

* `CanBeLoadedByProperties` trait is no longer required due to the migration of methods (see above).

## [1.0.0-alpha] - 2024-01-19 (Initial Release)

### Added

* Develop project to manage entities through the use of the decorator pattern.
* Cast field value data type by @strakers in https://github.com/strakers/drupal-entity-decorator/pull/4
* Group multiple decorators as collection by @strakers in https://github.com/strakers/drupal-entity-decorator/pull/5
* Add save method to access entity save by @strakers in https://github.com/strakers/drupal-entity-decorator/pull/7


[unreleased]: https://github.com/strakers/drupal-entity-decorator/compare/v1.0.0-alpha...HEAD
[1.0.0-alpha]: https://github.com/strakers/drupal-entity-decorator/releases/tag/v1.0.0-alpha