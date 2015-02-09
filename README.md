Phinject
========

[![Build Status](https://travis-ci.org/aztech-digital/phinject.png?branch=master)](https://travis-ci.org/aztech-digital/phinject)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aztech-digital/phinject/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aztech-digital/phinject/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/aztech-digital/phinject/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aztech-digital/phinject/?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/aztech/phinject.png)](http://hhvm.h4cc.de/package/aztech/phinject)

Phinject is a simple dependency injection container, with extensible activation & injection strategies.

## Setup

[Composer](https://getcomposer.org) is the only supported way of installing Phinject. From the root of your project, run the following command:

```
composer require aztech/phinject
```

## Usage

The documentation is available [here](./doc/).

We have a [getting started](./doc/01-Getting-started.md) guide, followed by more comprehensive documentation (although not yet exhaustive, but that will come):

- [Injection types](./doc/02-Injection-types.md)
- [References](./doc/03-References.md)
- [Activators](./doc/04-Activators.md)

## Credits

This library is originally a fork on `oliviermadre/dic-it`, available [here](https://github.com/oliviermadre/dic-it).

Most of the core features have however been refactored or rewritten, enough that I felt it was time to re-brand this package, in order to both prevent confusion with the original package, and because I did not like the name.
