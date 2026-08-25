<?php

/**
 * Loop.php
 *
 * A loop expression.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart\Data;

class Loop
{
    /**
     * The points in the loop
     *
     * @var array
     */
    private $aPoints = [];

    /**
     * @param float $iStart The first point
     * @param float $iEnd The last point
     * @param float|string $xStep The step between next points
     */
    public function __construct(float $iStart, float $iEnd, float|string $xStep)
    {
        $this->aPoints = [
            'start' => $iStart,
            'end' => $iEnd,
            'step' => $xStep,
        ];
    }

    /**
     * @return array
     */
    public function points(): array
    {
        return $this->aPoints;
    }
}
