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

use Jaxon\App\Config\ConfigManager;
use Jaxon\Charts\Chart\Card;
use Jaxon\Charts\Library\AbstractLibrary;
use Jaxon\Config\Config;
use Jaxon\Di\Container;
use Jaxon\Exception\SetupException;
use Jaxon\Plugin\AbstractResponsePlugin;
use Jaxon\Plugin\CssCode;
use Jaxon\Plugin\CssCodeGeneratorInterface;
use Jaxon\Plugin\JsCode;
use Jaxon\Plugin\JsCodeGeneratorInterface;
use Jaxon\Utils\Template\TemplateEngine;

use function array_filter;
use function array_map;
use function implode;

class ChartPlugin extends AbstractResponsePlugin implements CssCodeGeneratorInterface, JsCodeGeneratorInterface
{
    /**
     * @var string The plugin name
     */
    public const NAME = 'charts';

    /**
     * @var Config|null
     */
    private $xConfig = null;

    /**
     * @var array<AbstractLibrary>
     */
    private array $aLibraries = [];

    /**
     * @var array<AbstractLibrary>|null
     */
    private array|null $aActiveLibraries = null;

    /**
     * @var bool
     */
    private $bConfigProcessed = false;

    /**
     * The constructor
     *
     * @param Container $di
     * @param ConfigManager $xConfigManager
     * @param TemplateEngine $xTemplateEngine
     */
    public function __construct(private Container $di,
        private ConfigManager $xConfigManager, private TemplateEngine $xTemplateEngine)
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
     * @return Config
     */
    public function config(): Config
    {
        return $this->xConfig ??= $this->xConfigManager->getConfig('charts');
    }

    /**
     * Register a javascript chart library adapter.
     *
     * @param string $sClass
     * @param string $sLibraryName
     *
     * @return void
     */
    private function saveLibraryInContainer(string $sClass, string $sLibraryName): void
    {
        if(!$this->di->h($sClass))
        {
            $this->di->set($sClass, fn(Container $di) => $di->make($sClass));
        }
        // Set the alias, so the libraries can be found by their names.
        $this->di->alias("chart_library_$sLibraryName", $sClass);
    }

    /**
     * Register a javascript chart library adapter.
     *
     * @param string $sClassName
     * @param string $sLibraryName
     *
     * @return void
     */
    public function registerLibrary(string $sClassName, string $sLibraryName): void
    {
        if(isset($this->aLibraries[$sLibraryName]))
        {
            return;
        }

        // Save the library
        $this->aLibraries[$sLibraryName] = [
            'name' => $sLibraryName,
            'enabled' => false,
        ];

        // Register the library class in the container
        $this->saveLibraryInContainer($sClassName, $sLibraryName);
    }

    /**
     * @param string $sLibraryName
     *
     * @return string
     */
    public function renderJsCode(string $sLibraryName): string
    {
        return $this->xTemplateEngine->render("jaxon::charts::libs/{$sLibraryName}.js");
    }

    /**
     * @param string $sLibraryName
     *
     * @return string
     */
    public function renderCssCode(string $sLibraryName): string
    {
        return $this->xTemplateEngine->render("jaxon::charts::libs/{$sLibraryName}.css");
    }

    /**
     * Register the javascript chart libraries from config options.
     *
     * @return void
     * @throws SetupException
     */
    private function processLibraryConfig(): void
    {
        if($this->bConfigProcessed)
        {
            return;
        }

        // Register the 3rd party libraries
        $aLibraries = $this->config()->getOption('lib.ext', []);
        foreach($aLibraries as $sLibraryName => $sClassName)
        {
            $this->registerLibrary($sClassName, $sLibraryName);
        }

        // Set the other libraries in use
        $aLibraries = $this->config()->getOption('lib.use', []);
        foreach($aLibraries as $sLibraryName)
        {
            if(isset($this->aLibraries[$sLibraryName])) // Make sure the library exists
            {
                $this->aLibraries[$sLibraryName]['enabled'] = true;
            }
        }

        $this->bConfigProcessed = true;
    }

    /**
     * Get a chart library
     *
     * @param string $sLibraryName
     *
     * @return AbstractLibrary|null
     */
    private function getLibrary(string $sLibraryName): AbstractLibrary|null
    {
        $sKey = "chart_library_$sLibraryName";
        return $this->di->h($sKey) ? $this->di->g($sKey) : null;
    }

    /**
     * @return array<AbstractLibrary>
     */
    private function getActiveLibraries(): array
    {
        if($this->aActiveLibraries !== null)
        {
            return $this->aActiveLibraries;
        }

        $this->processLibraryConfig();

        // Set the enabled libraries.
        $cFilter = fn(array $aLibrary) => $aLibrary['enabled'];
        $cGetter = fn(array $aLibrary) => $this->getLibrary($aLibrary['name']);
        $aLibraries = array_filter($this->aLibraries, $cFilter);
        return $this->aActiveLibraries = array_map($cGetter, $aLibraries);
    }

    /**
     * @inheritDoc
     */
    public function getCssCode(): CssCode
    {
        $aUrls = [];
        $aCodes = [];
        foreach($this->getActiveLibraries() as $xLibrary)
        {
            $aUrls = [...$aUrls, ...$xLibrary->getCssUrls()];
            if(($sCode = $xLibrary->getCssCode()) !== '')
            {
                $aCodes[] = $sCode;
            }
        }

        return new CssCode(sCode: implode("\n", $aCodes), aUrls: $aUrls);
    }

    /**
     * @inheritDoc
     */
    public function getJsCode(): JsCode
    {
        $aUrls = [];
        $aCodes = [
            $this->xTemplateEngine->render("jaxon::charts::charts.js"),
        ];
        foreach($this->getActiveLibraries() as $xLibrary)
        {
            $aUrls = [...$aUrls, ...$xLibrary->getJsUrls()];
            if(($sCode = $xLibrary->getJsCode()) !== '')
            {
                $aCodes[] = $sCode;
            }
        }

        return new JsCode(sCode: implode("\n", $aCodes), aUrls: $aUrls);
    }

    /**
     * Create a graph card, to be displayed in a given HTML element.
     *
     * @param string $sSelector
     *
     * @return Card
     */
    public function card($sSelector): Card
    {
        $this->processLibraryConfig();

        return new Card($sSelector);
    }

    /**
     * Draw a card with thge specified chart library.
     *
     * @param Card $xCard
     * @param string $sLibraryName
     *
     * @return void
     */
    public function draw(Card $xCard, string $sLibraryName): void
    {
        if($this->aLibraries[$sLibraryName]['enabled'] ?? false)
        {
            $this->addCommand('charts.card', ['card' => $xCard, 'lib' => $sLibraryName]);
        }
    }
}
