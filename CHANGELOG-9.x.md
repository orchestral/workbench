# Changes for 9.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 9.5.0

Released: 2024-08-14

### Added

* Added `Orchestra\Workbench\Console\InstallCommand::$configurationBaseFile` option to define the default `testbench.yaml` stub.
* Utilise Symfony Console `InputOption::VALUE_NEGATABLE` feature on `workbench:install` and `workbench:devtool` command.
* Implements `Illuminate\Contracts\Console\PromptsForMissingInput` on `workbench:install` and `workbench:devtool` command.

## 9.4.1

Released: 2024-08-12

### Changes

* Update `workbench:devtool` command.

## 9.4.0

Released: 2024-08-10

### Changes

* Generate `User` model and `UserFactory` class via `workbench:install`.
* Update generated `DatabaseSeeder.php` to match Laravel 11 skeleton.

## 9.3.0

Released: 2024-08-06

### Changes

* Flush session when loading the start page via `composer run serve`.
* Disallow running `workbench:build`, `workbench:devtool` or `workbench:install` via `workbench:build` command.

## 9.2.0

Released: 2024-07-30

### Added

* Added support for `factories` discovery.

### Changes

* Small improvements to `workbench:devtool` command.

## 9.1.0

Released: 2024-05-21

### Added

* Added `nunomaduro/collision`.

### Changes

* PHPStan Improvements.

## 9.0.0

Released: 2024-03-13

### Changes

* Update support for Laravel Framework v11.
* Increase minimum PHP version to 8.2 and above (tested with 8.2 and 8.3).
* Swap `workbench:install` with `workbench:devtool` for smaller installation footprint.
