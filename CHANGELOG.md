# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning.

## [0.2.0] - 2025-08-10

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

## [0.1.0] - Initial
- Initial public release.

[0.2.0]: https://github.com/phariscope/Event/compare/0.1.0...0.2.0
