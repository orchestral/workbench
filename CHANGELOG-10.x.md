# Changes for 10.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 10.2.0

Released: 2026-07-22

### Changes

* Override `schedule:work` command.
* Update NPM Dependencies.

## 10.1.0

Released: 2026-03-25

### Changes

* Utilise `Orchestra\Canvas\Core\Actions`.
* Replace hardcoded Workbench\App\Models\User within `Orchestra\Workbench\Http\Controllers\Auth\RegisteredUserController` class
* Resolves user model from `TESTBENCH_USER_MODEL` environment variable (if available).

## 10.0.8

Released: 2026-01-12

### Changes

* Update `DatabaseSeeder` stub file.
* Update dependencies and assets.

## 10.0.7

Released: 2025-11-24

### Changes

* PHP 8.5 Compatibility.

## 10.0.6

Released: 2025-04-13

### Changes

* Remove `symfony/polyfill-php84`.

## 10.0.5

Released: 2025-04-08

### Changes

* Allows `Database\Factories\UserFactory` to be updated to Workbench namespace.

## 10.0.4

Released: 2025-04-06

### Fixes

* Fix the default installation to match Laravel application.

## 10.0.3

Released: 2025-03-20

### Changes

* Change `Orchestra\Workbench\StubRegistrar::swap()` method return type.
* Update dependencies and assets.

## 10.0.2

Released: 2025-03-18

### Changes

* Update `routes/console.stub`.

## 10.0.1

Released: 2025-03-06

### Changes

* Update dependencies and assets.

## 10.0.0

Released: 2025-02-24

### Changes

* Update support for Laravel Framework v12.

### Removed

* Remove deprecated `--skip-install` option from `workbench:devtool` command.
* Remove deprecated `--skip-devtool` option from `workbench:install` command.
