# Changes for 7.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 7.17.5

Released: 2025-04-06

### Fixes

* Fix the default installation to match Laravel application.

## 7.17.4

Released: 2025-03-20

### Changes

* Change `Orchestra\Workbench\StubRegistrar::swap()` method return type.
* Bump `orchestra/sidekick` dependency.

## 7.17.3

Released: 2025-03-06

### Changes

* Update dependencies and assets.

## 7.17.2

Released: 2025-02-19

### Changes

* Use `orchestra/sidekick`.

## 7.17.1

Released: 2025-01-23

### Changes

* Register routes via `ServiceProvider::booted()`.

## 7.17.0

Released: 2024-12-24

### Added

* Add `Orchestra\Workbench\Actions\ReplaceNamespaces` action class.
* Utilise `@no-named-arguments` PHP docblock.

### Changes

* Update `testbench.yaml` stub file.

## 7.16.0

Released: 2024-12-24

### Added

* Add `Orchestra\Workbench\StubRegistrar::replaceInFile()` method.

### Changes

* Allow to send options to `Orchestra\Workbench\Recipes\Command`.

## 7.15.2

Released: 2024-12-19

### Fixes

* Fix generated namespace via `workbench:install`.

## 7.15.1

Released: 2024-12-18

### Fixes

* Fix `workbench:install` generating migrate-fresh command with incorrect options.

## 7.14.0

Released: 2024-12-10

### Added

* Add `sync-skeleton` and `purge-skeleton` recipe for `workbench:build` command.
* Set default `laravel` configuration to either `@testbench` or `@testbench-dusk`
* Added following classes to interacts with Composer:
    - `Orchestra\Workbench\Actions\DumpComposerAutoloads`
    - `Orchestra\Workbench\Actions\ModifyComposer`

### Changes

* `workbench:install` should suggest creating `.env.dusk` equivalent file when `orchestra/testbench-dusk` is installed.
* Refactor `workbench:install` and add feature tests.

### Removed

* Remove `Orchestra\Workbench\Composer`.

## 7.13.0

Released: 2024-12-01

### Added

* Default authentication & dashboard using Laravel Breeze. This can be enabled by adding `workbench.auth` configuration to `true`.
* Add `workbench:purge-skeleton` as alias to `package:purge-skeleton` command.
* Add `workbench:sync-skeleton` as alias to `package:sync-skeleton` command.
* Add `applicationPath()` to `Orchestra\Workbench\Workbench` class.

### Changes

* Allows to customise the generated namespace instead of default to `Workbench\App` and `Workbench\Database`.
* Allows to override the default redirect after logged-in.

### Fixes

* Fixes the default `DatabaseSeeder` stub.

## 7.12.0

Released: 2024-11-19

### Added

* Added `Workbench::swapFile()` method to override the default generated stub file.
* Added `--database` option to `workbench:create-sqlite-db` command.
* Added `--database` and `--all` options to `workbench:drop-sqlite-db` command.

### Remove

* Remove `spatie/laravel-ray`.

## 7.11.0

Released: 2024-10-24

### Added

* Added `--basic` option to `workbench:install` and `workbench:devtool` command to simplify installation.
* Add `Orchestra\Workbench\Workbench::swapFile()` to override the default stub files:
    - `config`
    - `config.basic`
    - `gitignore`
    - `routes.api`
    - `routes.console`
    - `routes.web`
    - `seeders.database`

### Changes

* Utilise `Orchestra\Testbench\join_paths()` function.

## 7.10.0

Released: 2024-08-26

### Changes

* Allows following methods on `Orchestra\Workbench\Workbench` to accept arrays:
    - `laravelPath()`
    - `packagePath()`
    - `path()`

## 7.9.0

Released: 2024-08-14

### Added

* Added `Orchestra\Workbench\Console\InstallCommand::$configurationBaseFile` option to define the default `testbench.yaml` stub.
* Utilise Symfony Console `InputOption::VALUE_NEGATABLE` feature on `workbench:install` and `workbench:devtool` command.

## 7.8.1

Released: 2024-08-12

### Changes

* Update `workbench:devtool` command.

## 7.8.0

Released: 2024-08-10

### Changes

* Generate `User` model and `UserFactory` class via `workbench:install`.
* Update generated `DatabaseSeeder.php` to match Laravel 9 skeleton.

## 7.7.0

Released: 2024-08-06

### Changes

* Flush session when loading the start page via `composer run serve`.
* Disallow running `workbench:build`, `workbench:devtool` or `workbench:install` via `workbench:build` command.

## 7.6.0

Released: 2024-07-30

### Added

* Added support for `factories` discovery.

### Changes

* Small improvements to `workbench:devtool` command.

## 7.5.0

Released: 2024-05-21

### Added

* Added `nunomaduro/collision`.

### Changes

* PHPStan Improvements.

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
