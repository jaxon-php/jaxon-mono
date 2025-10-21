[![Build Status](https://github.com/jaxon-php/jaxon-attributes/actions/workflows/test.yml/badge.svg?branch=main)](https://github.com/jaxon-php/jaxon-attributes/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jaxon-php/jaxon-attributes/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/jaxon-php/jaxon-attributes/?branch=main)
[![codecov](https://codecov.io/gh/jaxon-php/jaxon-attributes/branch/main/graph/badge.svg?token=qbk2LXp78V)](https://codecov.io/gh/jaxon-php/jaxon-attributes)

[![Latest Stable Version](https://poser.pugx.org/jaxon-php/jaxon-attributes/v/stable)](https://packagist.org/packages/jaxon-php/jaxon-attributes)
[![Total Downloads](https://poser.pugx.org/jaxon-php/jaxon-attributes/downloads)](https://packagist.org/packages/jaxon-php/jaxon-attributes)
[![License](https://poser.pugx.org/jaxon-php/jaxon-attributes/license)](https://packagist.org/packages/jaxon-php/jaxon-attributes)

Attributes for the Jaxon library
=================================

This package provides attribute support for the Jaxon library.
The configuration options that are related to Jaxon classes can be set directly in the class files using attributes.

Installation
------------

Install this package with `composer`.
It requires `jaxon-php/jaxon-core` v5.1 or higher.

```shell
composer require jaxon-php/jaxon-attributes
```

Set the attribute config option.

```php
jaxon()->setOption('core.metadata.format', 'attributes');
```

> Note: The option must be set for a package if it defines classes with attributes.

When deploying the application in production, the metadata can be cached, to avoid performance issues.

```php
jaxon()->setOptions([
    'format' => 'attributes',
    'cache' => [
        'enabled' => true,
        'dir' => '/path/to/the/cache/dir',
    ],
], 'core.metadata');
```

Usage
-----

The following attributes are provided.

### #[Jaxon\Attributes\Attribute\Exclude]

It prevents a method or a class from being exported to javascript.
It takes an optional boolean parameter.

```php
use Jaxon\Attributes\Attribute\Exclude;

/**
 * #[Exclude(true)]
 */
class JaxonExample
{
// This class will not be exported to javascript.
}
```

```php
use Jaxon\Attributes\Attribute\Exclude;

class JaxonExample
{
    /**
     * #[Exclude]
     */
    public function doNot()
    {
        // This method will not be exported to javascript.
    }
}
```

### #[Jaxon\Attributes\Attribute\Upload]

It adds file upload to an ajax request.
It takes the id of the HTML field as a mandatory option.
It applies only to methods.

```php
use Jaxon\Attributes\Attribute\Upload;

class JaxonExample extends \Jaxon\App\Component
{
    /**
     * #[Upload(field: 'div-user-file')]
     */
    public function saveFile()
    {
        // Get the uploaded files.
        $files = $this->upload()->files();
    }
}
```

### #[Jaxon\Attributes\Attribute\Before]

It defines a method of the class as a callback to be called before processing the request.
It takes the name of the method as a mandatory parameter, and an array as optional parameters to be passed to the callback.
It applies to methods and classes.

```php
use Jaxon\Attributes\Attribute\Before;

class JaxonExample
{
    protected function funcBefore1()
    {
        // Do something
    }

    protected function funcBefore2($param1, $param2)
    {
        // Do something with parameters
    }

    /**
     * #[Before(call: 'funcBefore1')]
     * #[Before(call: 'funcBefore2', with: ['value1', 'value2'])]
     */
    public function action()
    {
    }
}
```

### #[Jaxon\Attributes\Attribute\After]

It defines a method of the class as a callback to be called after processing the request.
It takes the name of the method as a mandatory parameter, and an array as optional parameters to be passed to the callback.
It applies to methods and classes.

```php
use Jaxon\Attributes\Attribute\After;

class JaxonExample
{
    protected function funcAfter1()
    {
        // Do something
    }

    protected function funcAfter2($param)
    {
        // Do something with parameter
    }

    /**
     * #[After(call: 'funcAfter1')]
     * #[After(call: 'funcAfter2', with: ['value'])]
     */
    public function action()
    {
    }
}
```

### #[Jaxon\Attributes\Attribute\Callback]

It defines a javascript object to be used as callback when processing the ajax request.

It was added in version 2.2.0.

```php
use Jaxon\Attributes\Attribute\Callback;

/**
 * Default callback for all the requests to the class.
 *
 * #[Callback(name: 'jaxon.ajax.callback.example')]
 */
class JaxonExample
{
    /**
     * Specific callback for this method. It is added to the default class callback.
     *
     * #[Callback(name: 'jaxon.ajax.callback.action')]
     */
    public function action()
    {
    }
}
```

### #[Jaxon\Attributes\Attribute\Databag]

It defines a data bag to be appended to ajax requests to a method.
It takes the name of the data bag as a mandatory parameter.
It applies to methods and classes.

```php
use Jaxon\Attributes\Attribute\Databag;

class JaxonExample extends \Jaxon\App\Component
{
    /**
     * #[Databag(name: 'user')]
     */
    public function action()
    {
        // Update a value in the data bag.
        $count = $this->bag('user')->get('count', 0);
        $this->bag('user')->set('count', $count++);
    }
}
```

### #[Jaxon\Attributes\Attribute\Inject]

It defines an attribute that will be injected in a class.

When applied on methods and classes, it takes the name and the class of the attribute as parameters.

```php
use Jaxon\Attributes\Attribute\Inject;

class JaxonExample extends \Jaxon\App\Component
{
    /**
     * @var \App\Services\Translator
     */
     protected $translator;

    /**
     * #[Inject(attr: 'translator', class: \App\Services\Translator::class)]
     */
    public function translate(string $phrase)
    {
        // The $translator property is set from the DI container when this method is called.
        $phrase = $this->translator->translate($phrase);
    }
}
```

The class parameter is optional, and can be omitted if it is already specified by a `@var` attribute.

```php
use Jaxon\Attributes\Attribute\Inject;

class JaxonExample extends \Jaxon\App\Component
{
    /**
     * @var \App\Services\Translator
     */
     protected $translator;

    /**
     * #[Inject(attr: 'translator')]
     */
    public function translate(string $phrase)
    {
        // The $translator property is set from the DI container when this method is called.
        $phrase = $this->translator->translate($phrase);
    }
}
```

When applied on attributes, it takes the class of the attribute as only parameter, which can be omitted if it is already specified by a `@var` attribute.

```php
use Jaxon\Attributes\Attribute\Inject;

class JaxonExample extends \Jaxon\App\Component
{
    /**
     * #[Inject(class: \App\Services\Translator::class)]
     * @var \App\Services\Translator
     */
     protected $translator;

    public function translate(string $phrase)
    {
        // The $translator property is set from the DI container when this method is called.
        $phrase = $this->translator->translate($phrase);
    }
}
```

```php
use Jaxon\Attributes\Attribute\Inject;

class JaxonExample extends \Jaxon\App\Component
{
    /**
     * #[Inject]
     * @var \App\Services\Translator
     */
     protected $translator;

    public function translate(string $phrase)
    {
        // The $translator property is set from the DI container when this method is called.
        $phrase = $this->translator->translate($phrase);
    }
}
```
