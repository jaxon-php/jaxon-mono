<?php

use Jaxon\Jaxon;
use Jaxon\App\FuncComponent;
use Jaxon\Dialogs\Dialog\Library\Bootbox;
use Jaxon\Dialogs\Dialog\Library\Noty;

class HelloWorld extends FuncComponent
{
    public function sayHello(bool $isCaps, bool $bNotify = true)
    {
        $text = $isCaps ? 'HELLO WORLD!' : 'Hello World!';
        if(($bNotify))
        {
            // $this->response->confirmCommands(2, 'Skip text assignement?');
            $this->response->assign('div2', 'innerHTML', $text);
            // $this->response->confirmCommands(1, 'Skip text notification?');
            $this->response->dialog->success("div2 text is now $text");
        }
        else
        {
            // $this->response->confirmCommands(1, 'Skip text assignement?');
            $this->response->assign('div2', 'innerHTML', $text);
        }
    }

    public function setColor(string $sColor, bool $bNotify = true)
    {
        if(($bNotify))
        {
            // $this->response->confirmCommands(1, 'Skip color assignement?');
            $this->response->assign('div2', 'style.color', $sColor);
            // $this->response->confirmCommands(1, 'Skip color assignement?');
            $this->response->dialog->success("div2 color is now $sColor");
        }
        else
        {
            // $this->response->confirmCommands(1, 'Skip color assignement?');
            $this->response->assign('div2', 'style.color', $sColor);
        }
    }

    public function showDialog()
    {
        $buttons = [['title' => 'Close', 'class' => 'btn', 'click' => 'close']];
        $options = ['width' => 500];
        $this->response->dialog->show("Modal Dialog",
            "This modal dialog is powered by Bootbox!!", $buttons, $options);
    }
}

$jaxon = jaxon();

// $jaxon->setOption('core.debug.on', true);
$jaxon->setOption('core.prefix.class', 'Jaxon');

// Js options
$jaxon->setOption('js.lib.uri', '/js');
// $jaxon->setOption('js.lib.uri', '/exp/js/lib');
// $jaxon->setOption('js.app.minify', false);

// Dialog options
$jaxon->setAppOption('dialogs.default.modal', Bootbox::NAME);
$jaxon->setAppOption('dialogs.default.alert', Noty::NAME);
$jaxon->setAppOption('dialogs.default.confirm', Noty::NAME);

$jaxon->setAppOption('dialogs.confirm.title', 'Confirmer');
$jaxon->setAppOption('dialogs.confirm.yes', 'Oui');
$jaxon->setAppOption('dialogs.confirm.no', 'Non');

// Register object
$jaxon->register(Jaxon::CALLABLE_CLASS, HelloWorld::class);
