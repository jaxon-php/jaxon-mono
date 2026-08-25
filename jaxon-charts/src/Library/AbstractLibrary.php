<?php

/**
 * AbstractLibrary.php
 *
 * Base class for javascript chart libraries.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Library;

use Jaxon\Charts\ChartPlugin;
use Jaxon\Utils\Template\TemplateEngine;

use function array_map;
use function is_array;
use function rtrim;

abstract class AbstractLibrary
{
    /**
     * The base URL for js and css files
     *
     * @var string
     */
    protected string $sBaseUrl = '';

    /**
     * The css files
     *
     * @var array
     */
    protected array $aCssFiles = [];

    /**
     * The js files
     *
     * @var array
     */
    protected array $aJsFiles = [];

    /**
     * @param ChartPlugin $chart
     * @param TemplateEngine $xTemplateEngine
     */
    public function __construct(private ChartPlugin $chart,
        private TemplateEngine $xTemplateEngine)
    {}

    /**
     * @return TemplateEngine
     */
    protected function template(): TemplateEngine
    {
        return $this->xTemplateEngine;
    }

    /**
     * @return string
     */
    abstract public function getName(): string;

    /**
     * @param string $sFile The javascript file name
     *
     * @return string|null
     */
    private function getFileUrl(string $sFile): ?string
    {
        return rtrim($this->sBaseUrl, '/') . "/$sFile";
    }

    /**
     * @param string $sType
     *
     * @return bool
     */
    private function disabled(string $sType): bool
    {
        $sAssets = 'assets.' . $this->getName();
        $xConfig = $this->chart->config();
        return $xConfig->getOption($sAssets) === false ||
            $xConfig->getOption("$sAssets.$sType") === false;
    }

    /**
     * @return array
     */
    public function getCssUrls(): array
    {
        if($this->disabled('css'))
        {
            return [];
        }

        $aCssFiles = array_map($this->getFileUrl(...), $this->aCssFiles);
        $sAssets = 'assets.' . $this->getName();
        $xConfig = $this->chart->config();
        return is_array($aCssUrls = $xConfig->getOption("$sAssets.css")) ?
            [...$aCssFiles, ...$aCssUrls] : $aCssFiles;
    }

    /**
     * @return string
     */
    abstract public function getCssCode(): string;

    /**
     * @return array
     */
    public function getJsUrls(): array
    {
        if($this->disabled('js'))
        {
            return [];
        }

        $aJsFiles = array_map($this->getFileUrl(...), $this->aJsFiles);
        $sAssets = 'assets.' . $this->getName();
        $xConfig = $this->chart->config();
        return is_array($aJsUrls = $xConfig->getOption("$sAssets.js")) ?
            [...$aJsFiles, ...$aJsUrls] : $aJsFiles;
    }

    /**
     * @return string
     */
    abstract public function getJsCode(): string;
}
