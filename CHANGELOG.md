# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.1.0 - 2016-10-17

### Added

- [zfcampus/zf-apigility-doctrine#267](https://github.com/zfcampus/zf-apigility-doctrine/pull/267) adds
  support for version 3 releases of laminas-servicemanager and laminas-eventmanager,
  while retaining compatibility for v2 releases.

### Changes

- [zfcampus/zf-apigility-doctrine#267](https://github.com/zfcampus/zf-apigility-doctrine/pull/267) exposes the
  module to [laminas/laminas-component-installer](https://github.com/zendframework/zend-component-installer),
  exposing both `ZF\Apigility\Doctrine\Admin` and
  `ZF\Apigility\Doctrine\Server`. The former should be isntalled in the
  development configuration, and the latter in your application modules.
- [zfcampus/zf-apigility-doctrine#267](https://github.com/zfcampus/zf-apigility-doctrine/pull/267) updates
  dependency requirements for the following modules and components:
  - laminas-api-tools/api-tools-apigilty-admin ^1.5
  - phpro/zf-doctrine-hydration-module ^3.0
  - doctrine/DoctrineModule ^1.2
  - doctrine/DoctrineORMModule ^1.1
  - doctrine/DoctrineMongoODMModule ^0.11

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-apigility-doctrine#267](https://github.com/zfcampus/zf-apigility-doctrine/pull/267) removes
  support for PHP 5.5.

### Fixed

- [zfcampus/zf-apigility-doctrine#267](https://github.com/zfcampus/zf-apigility-doctrine/pull/267) adds a ton
  of tests to the module, and fixes a number of issues encountered.
