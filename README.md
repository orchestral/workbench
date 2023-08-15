Workbench Companion for Laravel Packages Development 
==============

Workbench Component is a simple package that has been designed to help you previews and tests your Laravel package.

[![tests](https://github.com/orchestral/workbench/workflows/tests/badge.svg?branch=master)](https://github.com/orchestral/workbench/actions?query=workflow%3Atests+branch%3Amaster)
[![Latest Stable Version](https://poser.pugx.org/orchestra/workbench/v/stable)](https://packagist.org/packages/orchestra/workbench)
[![Total Downloads](https://poser.pugx.org/orchestra/workbench/downloads)](https://packagist.org/packages/orchestra/workbench)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/workbench/v/unstable)](https://packagist.org/packages/orchestra/workbench)
[![License](https://poser.pugx.org/orchestra/workbench/license)](https://packagist.org/packages/orchestra/workbench)

### `testbench.yaml` Example

```yaml
workbench:
  start: /nova
  user: taylor@laravel.com
  guard: web
  sync:
    - from: ./public/
      to: public/vendor/nova
  build:
    - asset-publish
    - create-sqlite-db
    - migrate:refresh
  assets:
    - nova-assets

purge:
  directories: []
  files: []
```

### Commands

* `workbench:install`
* `workbench:build`
* `workbench:create-sqlite-db`
* `workbench:drop-sqlite-db`
