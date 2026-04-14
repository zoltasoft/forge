# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

---

## [v1.3.0] - 2025-12-03

### Changed

- Strengthened the entire codebase to comply with PHPStan Level 6, improving type accuracy, generics usage, and internal consistency.

- Updated hundreds of docblocks across API, Application, Domain, and Laravel layers for strict typing and clarity.

- Refined CQRS buses, mappers, argument resolvers, and hydrators to align with stricter type inference and ensure stability under static analysis.

- Improved reflection-based subsystems with safer type checks, narrowed return types, and stricter class-string enforcement.

- Enhanced internal Value Object auto-resolution and hydration behaviors with safer method expectations and clear contract definitions.

### Fixed

- Adjusted multiple DTOs, transformers, and domain components whose signatures were too broad under stricter analysis.

- Normalized filesystem and glob operations with proper guards against false return values.

- Fixed various list typing issues (list<string> vs array<int,string>) across query options, repositories, and routing caches.

- Resolved tests failing due to stricter string expectations (string|false cases).

- Updated Laravel route invoker behavior to properly satisfy Response data types.

### Internal

- Major refactor of internal hydrators, mappers, caches, and providers to ensure full compatibility with Level 6 typing.

Streamlined reflection cache data structures.

- Updated Rector and PHPStan configuration to support strict typing progressions.

- Extensive cleanup of legacy docblocks, improving static inference, AI tooling compatibility, and code readability.

---

## [v1.2.2] - 2025-12-03

### Fixed

- Resolved PHPUnit deprecation warning by updating the `phpunit.xml` configuration.
- Updated `Cqrs` service to rely on the new `DefaultMessageHydrator`.
- Updated `ZoltaServiceProvider` to bind `MessageHydratorInterface` to the new implementation.

### Changed

- Replaced deprecated `VOBuilderTrait` with the new hydration subsystem.
- Added `HydrationServiceProvider` and registered new hydration services.
- Cleaned up internal reflection and hydration logic for better testability and consistency.
- Updated Rector configuration to ignore valid behavior in `DefaultMessageHydrator`.

### Removed

- Removed deprecated `VOBuilderTrait` (fully replaced by the new hydration pipeline).

### Internal

- Introduced new `DefaultMessageHydrator` and `MessageHydratorInterface`.
- Improved hydration service architecture for commands, queries, and value objects.

---

## [v1.2.1] - 2025-12-02

### Fixed

- Corrected constructor DI in `InMemoryQueryBus`.
- Fixed signatures for `PositiveNumberRule` and `RegexRule`.
- Removed deprecated `DescriptionRule`.
- Improved duplicate-request error reporting in `RouteInvoker`.

### Changed

- Enhanced QA scripts (composer.json).
- Updated phpunit.xml and rector.php configurations.

---

## [v1.2.0] - 2025-12-01

### Changed

- Large internal refactor including Rector and PHPMD optimizations.
- Improved code quality, consistency, and architecture alignment.
- No public API changes; fully backward compatible.

### Internal

- Type safety improvements via PHPStan.
- Formatting consistency with Pint.
- Removal of obsolete constructs and dead code.

---

## [v1.1.2] - 2025-11-30

### Changed

- Full QA cleanup including:
  - PHPStan fixes and level improvements.
  - Pint formatting and consistency updates.
  - Test adjustments for updated behaviors.
  - Removal of obsolete internal classes and unused code paths.

---

## [v1.1.1] - 2025-11-30

### Fixed

- Corrected **request injection mismatch** in `RouteInvoker` to ensure accurate parameter mapping and runtime resolution.

---

## [v1.1.0] - 2025-11-29

### Added

- `php artisan zolta:dev` unified development runner.
- Real-time attribute route watcher with `zolta:routes:watch`.
- Automatic attribute route cache priming.
- New internal architecture for loaders, cache, and provider boot logic.

### Changed

- Improved zolta.php default configuration.
- Attribute route cache and loader behavior improved for DX.

---

## [v1.0.2] - 2025-11-29

### Fixed

- Replaced deprecated `HasView` attribute with new `View` attribute.
- Corrected view resolution logic in `AutoInvokeProxyController`.
- Updated `AttributeRouteCache`, `AttributeRouteLoader`, and tests.

---

## [v1.0.1] - 2025-11-29

### Added

- Stable release improvements.
- Internal command-map caching and route-map generation enhancements.

---

## [v1.0.0] - 2025-11-28

### Added

- Initial release of Zolta Forge.
- Attribute-based routing system.
- Command/Query/Event mapping system.
- Domain-driven service architecture.
