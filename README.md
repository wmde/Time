# DataValues Time

Library containing value objects to represent temporal information, parsers to turn user input
into such value objects, and formatters to turn them back into user consumable representations.

It is part of the [DataValues set of libraries](https://github.com/DataValues).

[![Build Status](https://secure.travis-ci.org/DataValues/Time.png?branch=master)](http://travis-ci.org/DataValues/Time)
[![Code Coverage](https://scrutinizer-ci.com/g/DataValues/Time/badges/coverage.png?s=c5db7b37576dedaedd28d27a0e5fda2b79e86da6)](https://scrutinizer-ci.com/g/DataValues/Time/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/DataValues/Time/badges/quality-score.png?s=3c66db1e59a8bf77f9e9a08760a92ca9c26538b9)](https://scrutinizer-ci.com/g/DataValues/Time/)

On [Packagist](https://packagist.org/packages/data-values/time):
[![Latest Stable Version](https://poser.pugx.org/data-values/time/version.png)](https://packagist.org/packages/data-values/time)
[![Download count](https://poser.pugx.org/data-values/time/d/total.png)](https://packagist.org/packages/data-values/time)

## Installation

The recommended way to use this library is via [Composer](http://getcomposer.org/).

### Composer

To add this package as a local, per-project dependency to your project, simply add a
dependency on `data-values/time` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
version 1.0 of this package:

    {
        "require": {
            "data-values/time": "1.0.*"
        }
    }

### Manual

Get the code of this package, either via git, or some other means. Also get all dependencies.
You can find a list of the dependencies in the "require" section of the composer.json file.
Then take care of autoloading the classes defined in the src directory.

## Tests

This library comes with a set up PHPUnit tests that cover all non-trivial code. You can run these
tests using the PHPUnit configuration file found in the root directory. The tests can also be run
via TravisCI, as a TravisCI configuration file is also provided in the root directory.

## Authors

DataValues Time has been written by the Wikidata team, as [Wikimedia Germany]
(https://wikimedia.de) employees for the [Wikidata project](https://wikidata.org/).

## Release notes

### 0.2 (2014-02-11)

Added features:

* TimeParser
* CalenderModelParser

### 0.1 (2013-11-17)

Initial release with these features:

* TimeValue
* TimeFormatter
* TimeIsoFormatter

## Links

* [DataValues Time on Packagist](https://packagist.org/packages/data-values/time)
* [DataValues Time on TravisCI](https://travis-ci.org/DataValues/Time)
