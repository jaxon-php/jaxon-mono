<?php

namespace Jaxon\Attributes;

use Jaxon\Attributes\AttributeReader;

use function Jaxon\jaxon;
use function php_sapi_name;

/**
 * Register the attribute reader into the Jaxon Inject container
 *
 * @return void
 */
function _register(): void
{
    $di = jaxon()->di();

    // Attribute reader
    $di->set(AttributeReader::class, fn() => new AttributeReader());

    $di->alias('metadata_reader_attributes', AttributeReader::class);
}

function register(): void
{
    // Do nothing if running in cli.
    if(php_sapi_name() !== 'cli')
    {
        _register();
    };
}

register();
