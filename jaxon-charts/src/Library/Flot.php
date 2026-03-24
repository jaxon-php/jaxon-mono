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

class Flot extends AbstractLibrary
{
    /**
     * The base URL for js and css files
     *
     * @var string
     */
    protected string $sBaseUrl = 'https://cdn.jsdelivr.net/npm/flot@4.2.6';

    /**
     * The js files
     *
     * @var array
     */
    protected array $aJsFiles = [
        'dist/es5/jquery.flot.min.js',
        'source/jquery.flot.pie.js',
    ];

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'flot';
    }
}