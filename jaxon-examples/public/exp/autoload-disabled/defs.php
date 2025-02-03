<?php

require dirname(__DIR__, 3) . '/includes/autoload.php';

use Jaxon\Jaxon;
use function Jaxon\jaxon;

$jaxon = jaxon();

$jaxon->setOption('js.lib.uri', '/js');
// $jaxon->setOption('core.debug.on', true);
$jaxon->setOption('core.prefix.class', '');

// Dialog options
$jaxon->app()->setOption('dialogs.default.modal', 'bootstrap');
$jaxon->app()->setOption('dialogs.default.alert', 'toastr');
$jaxon->app()->setOption('dialogs.toastr.options.alert.closeButton', true);
$jaxon->app()->setOption('dialogs.toastr.options.alert.positionClass', 'toast-top-center');
$jaxon->app()->setOption('dialogs.lib.use', ['tingle']);

// Request processing URI
$jaxon->setOption('core.request.uri', 'ajax.php');

// Disable autoload
// $jaxon->disableAutoload();

// Register the namespaces with a third-party autoloader
$loader = new Keradus\Psr4Autoloader;
$loader->register();
$loader->addNamespace('App', __DIR__ . '/../../../classes/namespace/app');
$loader->addNamespace('Ext', __DIR__ . '/../../../classes/namespace/ext');

// Add class dirs with namespaces
$jaxon->register(Jaxon::CALLABLE_DIR, __DIR__ . '/../../../classes/namespace/app', ['namespace' => 'App', 'autoload' => false]);
$jaxon->register(Jaxon::CALLABLE_DIR, __DIR__ . '/../../../classes/namespace/ext', ['namespace' => 'Ext', 'autoload' => false]);
