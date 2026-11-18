# Changelog

All notable changes to this project are in this file.

## 2.0.0 - TBD

### Changed

- PHPUnit 12.5 and PHPStan level 8.
- `AuthenticationResult::failed()` requires at least one message.
- `AuthenticationResult::getExpiresAt()` returns `?DateTimeImmutable`.
- `AuthenticationResult::getArrayCopy()` returns `expires_at` in the ATOM format.
- `AuthenticationResult::getFullName()` returns `null` when the user has no name.
- The constructor parameter `$expiresIn` of `AuthenticationResult` is now `$expires`.
- `AuthenticationResult::success()` accepts `null` for the first name, the last name and the expiry time.
- Errors that do not come from Facebook give the generic message `FacebookService::MESSAGE_FAILED`.
- The authorization URL uses the default Facebook scopes `public_profile` and `email`.
- The factory error message contains the full config path of the key.

### Added

- PHP 8.6 support.
- `AuthenticationServiceInterface::getState()`.
- `getFirstName()`, `getLastName()` and `getFullName()` in `AuthenticationResultInterface`.
- `ConfigProvider` alias from `AuthenticationServiceInterface` to `FacebookService`.
- `psr/container` as a direct requirement.
- Documentation for version 2 and an upgrade guide.

### Deprecated

- Nothing

### Removed

- PHP 8.2 support.
- `laminas/laminas-servicemanager` requirement.

### Fixed

- `AuthenticationResult::failed(null)` returned a valid result.
- The login failed with a PHP type error for users without an email address.
- The login failed for access tokens without an expiry time.
- The factory gave PHP warnings for missing config keys.
- The factory rejected the value `0`.
- The usage example did not return a response after the login.

## 1.5.0 - 2025-10-31

### Changed

- Nothing

### Added

- PHP 8.4 and 8.5 support.

### Deprecated

- Nothing

### Removed

- PHP 8.1 support (#25).

### Fixed

- Nothing

## 1.4.0 - 2025-03-05

### Changed

- Replaced Psalm with PHPStan (#22).

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Psalm.

### Fixed

- Nothing

## 1.3.1 - 2025-01-27

### Changed

- Bumped Psalm to 6.x (#20).
- Codecov runs on PHP 8.4.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.3.0 - 2025-01-21

### Changed

- Updated the PHP requirements.

### Added

- Support for `laminas/laminas-servicemanager` 4.x (#18).

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.2.3 - 2024-07-03

### Changed

- README: wrapper note.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.2.2 - 2024-05-03

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Documentation.

## 1.2.1 - 2024-05-03

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.2.0 - 2024-05-03

### Changed

- README.

### Added

- Documentation in `docs/book`.

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Package version issue.
- Typos.

## 1.1.2 - 2023-11-29

### Changed

- Codecov workflow.
- README.

### Added

- PHP 8.3 support.

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.1.1 - 2023-08-24

### Changed

- Nothing

### Added

- Codecov workflow and badge.

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.1.0 - 2023-07-07

### Changed

- Removed duplicate code.
- Added return types.

### Added

- Psalm, `cs-check` and static analysis.
- `ConfigProvider` test.

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Incorrect URLs.
- A not-found error.
- Indentation.

## 1.0.2 - 2022-11-18

### Changed

- README.md

### Added

- README.md

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.0.1 - 2022-11-18

### Changed

- README.md

### Added

- README.md

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.0.0 - 2022-11-16

### Changed

- Nothing

### Added

- Everything

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing
