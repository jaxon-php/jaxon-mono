<?php

/**
 * JAlertLibrary.php
 *
 * Adapter for the jAlert library.
 *
 * @package jaxon-dialogs
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2016 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-dialogs
 */

namespace Jaxon\Dialogs\Dialog\Library;

use Jaxon\Dialogs\Dialog\AbstractLibrary;
use Jaxon\App\Dialog\Library\AlertInterface;
use Jaxon\App\Dialog\Library\ConfirmInterface;

class JAlert extends AbstractLibrary implements AlertInterface, ConfirmInterface
{
    /**
     * @const The library name
     */
    const NAME = 'jalert';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): string
    {
        return 'https://cdn.jsdelivr.net/npm/jAlert@4.9.1/dist';
    }

    /**
     * @inheritDoc
     */
    public function getJs(): string
    {
        return $this->helper()->getJsCode('jAlert.min.js');
    }

    /**
     * @inheritDoc
     */
    public function getCss(): string
    {
        return $this->helper()->getCssCode('jAlert.min.css');
    }

    /**
     * @inheritDoc
     */
    public function getReadyScript(): string
    {
        return $this->helper()->render('jalert.js');
    }
}
