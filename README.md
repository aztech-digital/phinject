Phinject
========

[![Build Status](https://travis-ci.org/aztech-digital/phinject.png?branch=master)](https://travis-ci.org/aztech-digital/phinject)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/coverage/g/aztech-digital/phinject.svg?style=flat)](https://scrutinizer-ci.com/g/aztech-digital/phinject/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/g/aztech-digital/phinject.svg?style=flat)](https://scrutinizer-ci.com/g/aztech-digital/phinject/?branch=master)
[![HHVM Support](https://img.shields.io/hhvm/aztech/phinject.svg)](http://hhvm.h4cc.de/package/aztech/phinject)

Phinject is a simple dependency injection container, with extensible activation & injection strategies.

## Setup

[Composer](https://getcomposer.org) is the only supported way of installing Phinject. From the root of your project, run the following command:

```
composer require aztech/phinject
```

## Features

- Compatible with the [container-interop](https://github.com/container-interop/container-interop) specification
- Compatible with [delegate containers](https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup.md).
- YAML, JSON or PHP based configuration.
- Lazy-loading dependencies.
- Remote proxies (undocumented).
- Aliases (undocumented).
- Extensible configuration syntax (undocumented).

## Usage

The documentation is available [here](./doc/).

We have a [getting started](./doc/01-Getting-started.md) guide, followed by more comprehensive documentation (although not yet exhaustive, but that will come):

- [Injection types](./doc/02-Injection-types.md)
- [References](./doc/03-References.md)
- [Activators](./doc/04-Activators.md) 
- Lifecycle of objects (TODO: Write documentation)
- Remote objects (TODO: Write documentation)
- Extending the configuration syntax (TODO: Write documentation)

**Note** All the documentation examples are written using a YAML based configuration, however, you can also use JSON or PHP configuration files. (TODO: Document usage of PHP & JSON config file)

## Credits

This library is originally a fork on `oliviermadre/dic-it`, available [here](https://github.com/oliviermadre/dic-it).

Most of the core features have however been refactored or rewritten, enough that I felt it was time to re-brand this package, in order to both prevent confusion with the original package, and because I did not like the name.
