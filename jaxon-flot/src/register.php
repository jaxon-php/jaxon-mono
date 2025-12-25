<?php

namespace Jaxon\Flot;

use Jaxon\Flot\FlotPlugin;
use Jaxon\Utils\Template\TemplateEngine;

use function dirname;
use function Jaxon\jaxon;
use function php_sapi_name;

function _register(): void
{
    $jaxon = jaxon();
    // Register the template dir into the template renderer
    $jaxon->di()->set(FlotPlugin::class, function($c) {
        $xTemplateEngine = $c->g(TemplateEngine::class);
        $xTemplateEngine->addNamespace('jaxon::flot', dirname(__DIR__) . '/js');
        return new FlotPlugin($xTemplateEngine);
    });
    // Register an instance of this plugin
    $jaxon->registerPlugin(FlotPlugin::class, FlotPlugin::NAME);
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
