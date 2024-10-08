The Jaxon Ajax library
======================

Jaxon is an open source PHP library for easily creating Ajax web applications.
It allows into a web page to make direct Ajax calls to PHP classes that will in turn update its content, without reloading the entire page.

Jaxon is a fork of the Xajax PHP library.

This package is the Jaxon core library. Several plugins are provided in separate packages.

Features
--------

- All the Jaxon classes in a directory can be registered in one shot, possibly with a namespace.
- The configuration settings can be loaded from a file. Supported formats are JSON, YAML and PHP.
- The library provides pagination feature out of the box.
- The library is modular, and consists of a core package and several plugin packages.
- The javascript library is provided in a separated and javascript-only package, loaded by default from the [jsDelivr CDN](https://www.jsdelivr.com/projects/jaxon).
- The generated javascript classes are named according to their namespace or the subdirectory where they are located.
- All PHP packages install with `Composer`, are fully namespaced, and implement `PSR-4` autoloading.
- The library natively implements Ajax file upload.
- Up to version 3, the library runs on PHP versions 5.4 to 7.X. The version 4 runs on PHP versions 7.1 to 8.X.

Documentation
-------------

The project documentation is available in [English](http://www.jaxon-php.org/en/docs/) and [French](http://www.jaxon-php.org/fr/docs/).

Some sample codes (only for version 2.x) are provided in the [jaxon-php/jaxon-examples](https://github.com/jaxon-php/jaxon-examples) package, and demonstrated in [the website](http://www.jaxon-php.org/examples/).

Contribute
----------

- Issue Tracker: github.com/jaxon-php/jaxon-core/issues
- Source Code: github.com/jaxon-php/jaxon-core

License
-------

The project is licensed under the BSD license.
