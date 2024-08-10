# Changes for 9.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 9.4.0

Released: 2023-08-10

### Changes

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
