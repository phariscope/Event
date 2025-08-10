# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning.

## [Unreleased]

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
  - `LoggerTest`: verifies that listener exceptions are logged when a logger is configured, and that, without a logger, exceptions do not prevent other listeners from running.
  - `tests/Tools/SpyLogger`: lightweight PSR-3 test logger.

### Changed
- Event queue implementation switched from `array` with `array_shift()` to `SplQueue` for predictable FIFO performance.
- Documentation and PHPDoc aligned to state that listeners MUST NOT mutate event instances.
- Internal: renamed the boolean flag to `distributeImmediately` (internal usage only).

### Deprecated
- `EventDispatcher::distributeImmmediatly()` (misspelled). Use `distributeImmediately()` instead.
- Legacy types remain deprecated: `EventAbstract`, `EventPublisher`, `EventSubscriber`, `Tools\SpySubscriber`.

### Fixed
- Typos and wording in `README.md`.
- Static analysis and coding standards warnings in tests.

### Notes
- PSR-3 logging is optional. If you want to plug a logger, ensure `psr/log` is available in your project.
- Serializing `EventDispatcher` instances is not supported; the class is a singleton and intended for in-process use.

## [0.1.0] - Initial
- Initial public release.

[Unreleased]: https://github.com/phariscope/Event/compare/0.1.0...HEAD
