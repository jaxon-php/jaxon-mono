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

use Jaxon\Plugin\AbstractResponsePlugin;
use Jaxon\Plugin\JsCode;
use Jaxon\Plugin\JsCodeGeneratorInterface;
use Jaxon\Utils\Template\TemplateEngine;
use Jaxon\Flot\Plot\Plot;

class FlotPlugin extends AbstractResponsePlugin implements JsCodeGeneratorInterface
{
    /**
     * @var string The plugin name
     */
    public const NAME = 'flot';

    /**
     * @var string
     */
    private const JS_LIB_URL = 'https://cdn.jsdelivr.net/npm/flot@4.2.6/dist/es5/jquery.flot.min.js';

    /**
     * The constructor
     *
     * @param TemplateEngine $xTemplateEngine
     */
    public function __construct(private TemplateEngine $xTemplateEngine)
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
        return '5.0.0';
    }

    /**
     * @inheritDoc
     */
    public function getJsCode(): JsCode
    {
        $sCode = $this->xTemplateEngine->render('jaxon::flot::flot.js');
        return new JsCode(sCode: $sCode, aUrls: [self::JS_LIB_URL]);
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
    public function draw(Plot $xPlot): void
    {
        // The "flot.plot" command is registered by the plugin script.
        $this->addCommand('flot.plot', ['plot' => $xPlot]);
    }
}
