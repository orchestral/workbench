# Changes for 11.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 11.2.0

Released: 2026-07-22

### Changes

* Override `schedule:work` command.
* Update NPM Dependencies.

## 11.1.0

Released: 2026-03-25

### Changes

* Replace hardcoded Workbench\App\Models\User within `Orchestra\Workbench\Http\Controllers\Auth\RegisteredUserController` class
* Resolves user model from `TESTBENCH_USER_MODEL` environment variable (if available).

## 11.0.1

Released: 2026-03-19

### Fixes

* Fix generated `user-factory.stub`.

## 11.0.0

Released: 2026-03-16

### Changes

* Update support for Laravel Framework v13.
