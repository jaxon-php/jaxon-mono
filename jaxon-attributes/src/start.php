<?php

namespace Jaxon\Attributes;

use Jaxon\Attributes\AttributeParser;
use Jaxon\Attributes\AttributeReader;

use function Jaxon\jaxon;
use function sys_get_temp_dir;

/**
 * Register the attribute reader into the Jaxon DI container
 *
 * @return void
 */
function registerAttributesReader()
{
    $di = jaxon()->di();

    $sCacheDirKey = 'jaxon_attributes_cache_dir';
    $di->val($sCacheDirKey, sys_get_temp_dir());

        // Attribute parser
    $di->set(AttributeParser::class, function($di) use($sCacheDirKey) {
        return new AttributeParser($di->g($sCacheDirKey));
    });

    // Attribute reader
    $di->set(AttributeReader::class, function($di) use($sCacheDirKey) {
        return new AttributeReader($di->g(AttributeParser::class), $di->g($sCacheDirKey));
    });
    $di->alias('metadata_reader_attributes', AttributeReader::class);
}

// Register the attribute reader.
registerAttributesReader();
