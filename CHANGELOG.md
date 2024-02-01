# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Roadmap]
What's in the pipeline to come. (Click title for changes.)

## [Unreleased]

Committed for the next release:

### Added

* New static method on the `Collection` class, `create` to make Collections on the fly with easy chaining of 
subsequent methods.

## [1.0.0-beta] - 2024-01-22

### Added

* Decorators for Block and BlockContent entities.
* Documentation file for covering usage best practices.
* New methods on the `DateRange` class, `hasStarted` and `hasEnded` for more comparison options.
* New methods on the `Collection` class, `forEach`, `limit`, `reduce`, and `values` for more flexible usage.
* New methods on the `EntityDecoratorBase` class: `getEntityStorage` for more variable loading functions, and 
`loadMultiple` for loading a collection of decorated entities by ID.

### Changed

* Collection methods `first()` and `last()` now return `null` instead of `false` when no value is found.
* Migrated `loadByProperties()` and related methods from trait to `EntityDecoratorBase` class.
* `UserDecorator` class now implements `Drupal\Core\Session\AccountInterface` to better satisfy usage requirements. Can 
now function as a drop-in replacement for User and Account classes (for decorator purposes).
* Updated `Collection::keys()` method to return a Collection instance instead of an array.
* The `IsOwned::getOwner()` method now accepts entities that have the `getOwner` method but do not implement the 
`EntityOwnerTrait` trait.

### Deprecated

* `CanBeLoadedByProperties` trait is no longer required due to the migration of methods (see above).
* Replace usages of `EntityDecoratorBase::getEntitiesByProperties` with new `EntityDecoratorBase::getEntityStorage`. 
Note that replacement is not drop-in usage. The respective method must be used from the returned EntityStorage object.

### Fixed

* Bug preventing the `loadOneByProperties` method from loading entities.
* Bug preventing `WebformSubmissionDecorator` from accessing webform element field data.

## [1.0.0-alpha] - 2024-01-19 (Initial Release)

### Added

* Develop project to manage entities through the use of the decorator pattern.
* Cast field value data type by @strakers in https://github.com/strakers/drupal-entity-decorator/pull/4
* Group multiple decorators as collection by @strakers in https://github.com/strakers/drupal-entity-decorator/pull/5
* Add save method to access entity save by @strakers in https://github.com/strakers/drupal-entity-decorator/pull/7


[roadmap]: https://github.com/strakers/drupal-entity-decorator/compare/HEAD...develop
[unreleased]: https://github.com/strakers/drupal-entity-decorator/compare/v1.0.0-beta...HEAD
[1.0.0-beta]: https://github.com/strakers/drupal-entity-decorator/compare/v1.0.0-alpha...v1.0.0-beta
[1.0.0-alpha]: https://github.com/strakers/drupal-entity-decorator/releases/tag/v1.0.0-alpha