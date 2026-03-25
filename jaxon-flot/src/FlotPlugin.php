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
use Jaxon\Plugin\CssCode;
use Jaxon\Plugin\CssCodeGeneratorInterface;
use Jaxon\Plugin\JsCode;
use Jaxon\Plugin\JsCodeGeneratorInterface;
use Jaxon\Utils\Template\TemplateEngine;
use Jaxon\Flot\Chart\Card;

class FlotPlugin extends AbstractResponsePlugin implements CssCodeGeneratorInterface, JsCodeGeneratorInterface
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
     * @var string
     */
    private const JS_PIE_URL = 'https://cdn.jsdelivr.net/npm/flot@4.2.6/source/jquery.flot.pie.js';

    /**
     * @var bool
     */
    private bool $usePie = false;

    /**
     * The constructor
     *
     * @param TemplateEngine $xTemplateEngine
     */
    public function __construct(private TemplateEngine $xTemplateEngine)
    {}

    /**
     * @param bool $usePie
     *
     * @return static
     */
    public function usePie(bool $usePie): static
    {
        $this->usePie = $usePie;
        return $this;
    }

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
        return '5.0.3';
    }

    /**
     * @inheritDoc
     */
    public function getCssCode(): CssCode
    {
        $sCode = $this->xTemplateEngine->render('jaxon::flot::flot.css');
        return new CssCode(sCode: $sCode);
    }

    /**
     * @inheritDoc
     */
    public function getJsCode(): JsCode
    {
        $sCode = $this->xTemplateEngine->render('jaxon::flot::flot.js');
        $aUrls = $this->usePie ? [self::JS_LIB_URL, self::JS_PIE_URL] : [self::JS_LIB_URL];
        return new JsCode(sCode: $sCode, aUrls: $aUrls);
    }

    /**
     * Create a Card instance.
     *
     * @param string $sSelector
     * @param array $aOptions
     *
     * @return Card
     */
    public function card($sSelector, array $aOptions = []): Card
    {
        return new Card($sSelector, $aOptions);
    }

    /**
     * Draw a Card in a given HTML element.
     *
     * @return void
     */
    public function draw(Card $xCard): void
    {
        // The "flot.card" command is registered by the plugin script.
        $this->addCommand('flot.card', ['card' => $xCard]);
    }
}
