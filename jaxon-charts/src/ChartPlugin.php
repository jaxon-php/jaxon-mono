<?php

/**
 * FlotPlugin.php
 *
 * Javascript charts for Jaxon.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts;

use Jaxon\Plugin\AbstractResponsePlugin;
use Jaxon\Plugin\CssCode;
use Jaxon\Plugin\CssCodeGeneratorInterface;
use Jaxon\Plugin\JsCode;
use Jaxon\Plugin\JsCodeGeneratorInterface;
use Jaxon\Utils\Template\TemplateEngine;
use Jaxon\Charts\Chart\Card;

class ChartPlugin extends AbstractResponsePlugin implements CssCodeGeneratorInterface, JsCodeGeneratorInterface
{
    /**
     * @var string The plugin name
     */
    public const NAME = 'charts';

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
        return '1.0.0';
    }

    /**
     * @inheritDoc
     */
    public function getCssCode(): CssCode
    {
        return new CssCode();
    }

    /**
     * @inheritDoc
     */
    public function getJsCode(): JsCode
    {
        return new JsCode();
    }

    /**
     * Create a Card instance.
     *
     * @param string $sSelector
     *
     * @return Card
     */
    public function card($sSelector): Card
    {
        return new Card($sSelector);
    }

    /**
     * Draw a Card in a given HTML element.
     *
     * @param Card $xCard
     *
     * @return void
     */
    public function draw(Card $xCard): void
    {
        // The "charts.card" command is registered by the plugin script.
        $this->addCommand('charts.card', ['card' => $xCard]);
    }
}
