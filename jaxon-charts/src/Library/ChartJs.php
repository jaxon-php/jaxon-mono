<?php

/**
 * Flot.php
 *
 * Adapter for the Flot library.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Library;

class ChartJs extends AbstractLibrary
{
    /**
     * The base URL for js and css files
     *
     * @var string
     */
    protected string $sBaseUrl = 'https://cdn.jsdelivr.net/';

    /**
     * The js files
     *
     * @var array
     */
    protected array $aJsFiles = [
        'npm/chart.js@4.5.1/dist/chart.umd.min.js',
        'gh/imhvost/chart-utils/dist/chart-utils.min.js'
    ];

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'chartjs';
    }

    /**
     * @return string
     */
    public function getCssCode(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getJsCode(): string
    {
        return $this->template()->render("jaxon::charts::chartjs/chart.js");
    }
}
