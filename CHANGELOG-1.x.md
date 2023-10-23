# Changes for 1.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 1.0.0

Unreleased

### Added

* Generate `workbench/routes/web.php`, `workbench/routes/api.php` and `workbench/routes/console.php` files from `workbench:install` command.
* Generate `Workbench\App\Providers\WorkbenchServiceProvider` from `workbench:install` command.

### Removed

* Removed `Orchestra\Workbench\Workbench::discover()` method, replaced in `orchestra/testbench-core` using `Orchestra\Testbench\Foundation\Bootstrap\DiscoverRoutes`.
