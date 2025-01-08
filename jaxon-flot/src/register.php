<?php

namespace Jaxon\Flot;

use Jaxon\Flot\FlotPlugin;
use Jaxon\Utils\Template\TemplateEngine;

use function Jaxon\jaxon;
use function php_sapi_name;

function register()
{
    // Do nothing if running in cli.
    if(php_sapi_name() === 'cli')
    {
        return;
    };

    $jaxon = jaxon();

    // Register the template dir into the template renderer
    $jaxon->di()->set(FlotPlugin::class, function($c) {
        $xTemplateEngine = $c->g(TemplateEngine::class);
        $xTemplateEngine->addNamespace('jaxon::flot', realpath(__DIR__ . '/../templates'));
        return new FlotPlugin($xTemplateEngine);
    });

    // Register an instance of this plugin
    $jaxon->registerPlugin(FlotPlugin::class, FlotPlugin::NAME);
}

register();
