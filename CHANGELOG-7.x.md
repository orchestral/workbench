# Changes for 7.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 7.4.0

Released: 2024-03-13

### Changes

* Swap `workbench:install` with `workbench:devtool` for smaller installation footprint.

## 7.3.0

Released: 2024-03-03

### Added

* Added ability to configure `build` commands with arguments and options.

## 7.2.0

Released: 2023-12-06

### Added

* Supports Workbench `discovers.components` configuration.

## 7.1.0

Released: 2023-12-04

### Changes

* Add `#[Override]` attribute to relevant methods, this require `symfony/polyfill-php83` as backward compatibility for PHP 8.1 and 8.2.
* Move `spatie/laravel-ray` from `require-dev` to `require`.

## 7.0.0

Released: 2023-11-07

### Changes

Restructure releases for Workbench to follow Testbench version. `7.x` releases will only be compatible with Testbench `7.x` and Laravel `9.x`.
