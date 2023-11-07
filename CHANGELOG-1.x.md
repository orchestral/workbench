# Changes for 1.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/workbench`.

## 1.0.1

Released: 2023-10-31

### Added

* Added Workbench information to `about` artisan command.

### Changes

* Disable Composer script timeout to `composer run serve` generator.

## 1.0.0

Released: 2023-10-24

### Added

* Generate `workbench/routes/web.php`, `workbench/routes/api.php` and `workbench/routes/console.php` files from `workbench:install` command.
* Generate `Workbench\App\Providers\WorkbenchServiceProvider` from `workbench:install` command.

### Removed

* Removed `Orchestra\Workbench\Workbench::discover()` method, replaced in `orchestra/testbench-core` using `Orchestra\Testbench\Foundation\Bootstrap\DiscoverRoutes`.
