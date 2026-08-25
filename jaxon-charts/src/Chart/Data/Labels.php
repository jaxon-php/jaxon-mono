<?php

/**
 * Labels.php
 *
 * An expression to make labels for ticks and graphs.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart\Data;

trait Labels
{
    /**
     * @var array
     */
    private $aLabels = [];

    /**
     * @param string $sJsLabel A javascript function to make labels
     *
     * @return static
     */
    public function labels(string $sJsLabel): static
    {
        $this->aLabels = ['func' => $sJsLabel];
        return $this;
    }
}
