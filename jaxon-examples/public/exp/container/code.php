<?php

use Jaxon\Jaxon;
use Jaxon\App\CallableClass;
use function Jaxon\jaxon;

use Service\ExampleInterface;

// Register the namespace with a third-party autoloader
$loader = new Keradus\Psr4Autoloader;
$loader->register();
$loader->addNamespace('Service', $classesDir . '/namespace/service');

class HelloWorld extends CallableClass
{
    protected $service;

    public function __construct(ExampleInterface $service)
    {
        $this->service = $service;
    }

    public function sayHello(bool $isCaps, $sMessage)
    {
        $sMessage = $this->service->message($isCaps) . ', ' . $sMessage;
        $this->response->assign('div2', 'innerHTML', $sMessage);
    }

    public function setColor(string $sColor)
    {
        $sColor = $this->service->color($sColor);
        $this->response->assign('div2', 'style.color', $sColor);
    }
}

// Register object
$jaxon = jaxon();

$jaxon->app()->setup($configDir . '/container.php', 'lib');

// Request processing URI
$jaxon->setOption('core.request.uri', 'ajax.php');
$jaxon->setOption('core.decode_utf8', true);

$jaxon->register(Jaxon::CALLABLE_CLASS, HelloWorld::class);
