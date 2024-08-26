# Changes for 8.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 8.10.0

Released: 2024-08-26

### Changes

* Allows following methods on `Orchestra\Workbench\Workbench` to accept arrays:
    - `laravelPath()`
    - `packagePath()`
    - `path()`

## 8.9.0

Released: 2024-08-14

### Added

* Added `Orchestra\Workbench\Console\InstallCommand::$configurationBaseFile` option to define the default `testbench.yaml` stub.
* Utilise Symfony Console `InputOption::VALUE_NEGATABLE` feature on `workbench:install` and `workbench:devtool` command.
* Implements `Illuminate\Contracts\Console\PromptsForMissingInput` on `workbench:install` and `workbench:devtool` command.

## 8.8.1

Released: 2024-08-12

### Changes

* Update `workbench:devtool` command.

## 8.8.0

Released: 2024-08-10

### Changes

* Generate `User` model and `UserFactory` class via `workbench:install`.
* Update generated `DatabaseSeeder.php` to match Laravel 10 skeleton.

## 8.7.0

Released: 2024-08-06

### Changes

* Flush session when loading the start page via `composer run serve`.
* Disallow running `workbench:build`, `workbench:devtool` or `workbench:install` via `workbench:build` command.

## 8.6.0

Released: 2024-07-30

### Added

* Added support for `factories` discovery.

### Changes

* Small improvements to `workbench:devtool` command.

## 8.5.0

Released: 2024-05-21

### Added

* Added `nunomaduro/collision`.

### Changes

* PHPStan Improvements.

## 8.4.0

Released: 2024-03-13

### Changes

* Swap `workbench:install` with `workbench:devtool` for smaller installation footprint.

## 8.3.0

Released: 2024-03-03

### Added

* Added ability to configure `build` commands with arguments and options.

## 8.2.1

Released: 2023-12-28

### Changes

* Utilise `Illuminate\Filesystem\join_paths()` function.

## 8.2.0

Released: 2023-12-06

### Added

* Supports Workbench `discovers.components` configuration.

## 8.1.0

Released: 2023-12-04

### Changes

* Add `#[Override]` attribute to relevant methods, this require `symfony/polyfill-php83` as backward compatibility for PHP 8.1 and 8.2.
* Move `spatie/laravel-ray` from `require-dev` to `require`.

## 8.0.0

Released: 2023-11-07

### Changes

Restructure releases for Workbench to follow Testbench version. `8.x` releases will only be compatible with Testbench `8.x` and Laravel `10.x`.
