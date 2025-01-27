<?php

namespace Jaxon\Flot;

use Jaxon\App\Config\ConfigManager;
use Jaxon\Flot\FlotPlugin;
use Jaxon\Utils\Template\TemplateEngine;

use function Jaxon\jaxon;
use function php_sapi_name;

function _register()
{
    $jaxon = jaxon();

    // Register the template dir into the template renderer
    $jaxon->di()->set(FlotPlugin::class, function($c) {
        $xTemplateEngine = $c->g(TemplateEngine::class);
        $xTemplateEngine->addNamespace('jaxon::flot', realpath(__DIR__ . '/../js'));
        return new FlotPlugin($c->g(ConfigManager::class), $xTemplateEngine);
    });

    // Register an instance of this plugin
    $jaxon->registerPlugin(FlotPlugin::class, FlotPlugin::NAME);
}

function register()
{
    // Do nothing if running in cli.
    if(php_sapi_name() !== 'cli')
    {
        _register();
    };
}

register();
