# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning.

## [1.2.1] - 2025-09-19

### Fixed
- hasSubscriber accepts both class and object
- Minor improvements and code quality enhancements
- Documentation refinements

## [1.2.0] - 2025-08-10

### Added
- `EventDispatcher::distributeImmediately()` to enable automatic distribution on each `dispatch()`.
- Optional PSR-3 logging support via `EventDispatcher::setLogger(?Psr\Log\LoggerInterface)`. Listener exceptions are logged without interrupting the dispatch flow.
- `declare(strict_types=1);` across source and tests.
- Documentation improvements:
  - Immediate distribution usage.
  - Aggregate example (`AccountCreated`), listener wiring, and queue processing.
  - Optional logging section with example.
  - Event immutability policy.
- Tests:
- Logger tests (with and without configured logger).
- PSR-14 semantics tests (stop propagation, ordering) integrated with legacy API.
- Quality: Mutation testing (Infection) MSI: 100%.

### Changed
- Event queue implementation switched from `array` with `array_shift()` to `SplQueue` for predictable FIFO performance.
- Documentation and PHPDoc aligned to state that listeners MUST NOT mutate event instances.
- Internal: renamed the boolean flag to `distributeImmediately`.
- PSR-14 semantics integrated internally: legacy dispatcher now honors `Psr\\EventDispatcher\\StoppableEventInterface` (propagation stops after current listener when flagged).

### Deprecated
- `EventDispatcher::distributeImmmediatly()` (misspelled). Use `distributeImmediately()` instead.
- Legacy types remain deprecated: `EventAbstract`, `EventPublisher`, `EventSubscriber`, `Tools\SpySubscriber`.

### Fixed
- Typos and wording in `README.md`.
- Static analysis and coding standards warnings in tests.

### Notes
- PSR-3 logging is optional. If you want to plug a logger, ensure `psr/log` is available in your project.
- Serializing `EventDispatcher` instances is not supported; the class is a singleton and intended for in-process use.
- PSR-14 classes are not exposed publicly; integration is internal to preserve the legacy API surface.

## [1.1.0] - 2024-02-29

### Fixed
- Fixed listener loop event handling - ability to call another event inside a listener

## [1.0.7] - 2024-02-29

### Fixed
- Fixed listener loop event handling - ability to call another event inside a listener

## [1.0.6] - 2023-10-05

### Changed
- Refactored: moved functions for better organization

## [1.0.5] - 2023-10-01

### Changed
- Made `occurredOn` property protected in Event class

## [1.0.4] - 2023-09-30

### Fixed
- Fixed composer configuration

## [1.0.3] - 2023-09-27

### Added
- Added default datetime handling

## [1.0.2] - 2023-09-24

### Changed
- Use EventAbstract instead of EventInterface

## [1.0.1] - 2023-09-24

### Changed
- Use EventAbstract instead of EventInterface

## [1.0.0] - 2023-09-24

### Added
- Initial stable release
- Event creation, publish and distribute functionality
- Core event dispatcher implementation

[1.2.1]: https://github.com/phariscope/Event/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/phariscope/Event/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/phariscope/Event/compare/1.0.7...1.1.0
[1.0.7]: https://github.com/phariscope/Event/compare/1.0.6...1.0.7
[1.0.6]: https://github.com/phariscope/Event/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/phariscope/Event/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/phariscope/Event/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/phariscope/Event/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/phariscope/Event/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/phariscope/Event/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/phariscope/Event/releases/tag/1.0.0
