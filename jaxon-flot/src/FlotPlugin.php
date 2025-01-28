<?php

/**
 * FlotPlugin.php - Javascript charts for Jaxon with the Flot library.
 *
 * @package jaxon-flot
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2017 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-flot
 */

namespace Jaxon\Flot;

use Jaxon\App\Config\ConfigManager;
use Jaxon\Plugin\AbstractResponsePlugin;
use Jaxon\Plugin\Code\JsCode;
use Jaxon\Utils\Template\TemplateEngine;
use Jaxon\Flot\Plot\Plot;

class FlotPlugin extends AbstractResponsePlugin
{
    /**
     * @const The plugin name
     */
    public const NAME = 'flot';

    /**
     * @const
     */
    private const JS_LIB_URL = 'https://cdn.jsdelivr.net/npm/flot@4.2.6/dist/es5/jquery.flot.min.js';

    /**
     * @const
     */
    private const JS_SCRIPT_URL = 'https://cdn.jsdelivr.net/gh/jaxon-php/jaxon-flot@main/js/flot.js';

    /**
     * The constructor
     *
     * @param ConfigManager $xConfigManager
     * @param TemplateEngine $xTemplateEngine
     */
    public function __construct(private  ConfigManager $xConfigManager,
        private TemplateEngine $xTemplateEngine)
    {}

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
    public function getHash(): string
    {
        // The version number is used as hash
        return '3.1.0';
    }

    /**
     * @inheritDoc
     */
    public function getJs(): string
    {
        return '<script type="text/javascript" src="' . self::JS_LIB_URL . '"></script>';
    }

    /**
     * @inheritDoc
     */
    public function getScript(): string
    {
        return $this->xConfigManager->getOption('js.app.export', false) ? '' :
            $this->xTemplateEngine->render('jaxon::flot::flot.js');
    }

    /**
     * @inheritDoc
     */
    public function getJsCode(): ?JsCode
    {
        if(!$this->xConfigManager->getOption('js.app.export', false))
        {
            return null;
        }

        $xJsCode = new JsCode();
        $xJsCode->aFiles[] = self::JS_SCRIPT_URL;
        return $xJsCode;
    }

    /**
     * Create a Plot instance.
     *
     * @param string        $sSelector            The jQuery selector
     *
     * @return Plot
     */
    public function plot($sSelector): Plot
    {
        return new Plot($sSelector);
    }

    /**
     * Draw a Plot in a given HTML element.
     *
     * @return void
     */
    public function draw(Plot $xPlot)
    {
        $this->addCommand('flot.plot', ['plot' => $xPlot]);
    }
}
