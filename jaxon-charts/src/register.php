<?php

namespace Jaxon\Charts;

use Jaxon\Utils\Template\TemplateEngine;

use function dirname;
use function Jaxon\jaxon;
use function php_sapi_name;

function _register(): void
{
    $jaxon = jaxon();
    // Register the template dir into the template renderer
    $jaxon->di()->set(ChartPlugin::class, function($c) {
        $xTemplateEngine = $c->g(TemplateEngine::class);
        $xTemplateEngine->addNamespace('jaxon::charts', dirname(__DIR__) . '/js');
        return new ChartPlugin($xTemplateEngine);
    });
    // Register an instance of this plugin
    $jaxon->registerPlugin(ChartPlugin::class, ChartPlugin::NAME);
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
