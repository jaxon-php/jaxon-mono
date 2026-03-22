<?php

/**
 * OptionTrait.php
 *
 * Options for cards and charts.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart\Option;

trait OptionTrait
{
    /**
     * @var array
     */
    protected $aOptions = [];

    /**
     * Add an option
     *
     * @param string $sName
     * @param mixed $xValue
     *
     * @return static
     */
    public function option(string $sName, mixed $xValue): static
    {
        $this->aOptions[$sName] = $xValue;
        return $this;
    }

    /**
     * Add options
     *
     * @param array $aOptions
     *
     * @return static
     */
    public function options(array $aOptions): static
    {
        foreach($aOptions as $sName => $xValue)
        {
            $this->option($sName, $xValue);
        }
        return $this;
    }
}
