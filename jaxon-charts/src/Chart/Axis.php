<?php

/**
 * Axis.php
 *
 * An axis for the graphs in a card.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart;

use Jaxon\Charts\Chart\Data\Axis\Ticks;
use Jaxon\Charts\Chart\Option\OptionTrait;
use JsonSerializable;

class Axis implements JsonSerializable
{
    use OptionTrait;

    /**
     * @var Ticks|null
     */
    protected Ticks|null $xTicks;

    /**
     * The constructor
     *
     * @param array $aOptions
     */
    public function __construct(array $aOptions = [])
    {
        $this->options($aOptions);
    }

    /**
     * Get the axis series
     *
     * @return Ticks
     */
    public function ticks(): Ticks
    {
        return $this->xTicks ??= new Ticks();
    }

    /**
     * Convert this object to another object more suitable for json format.
     *
     * This is a method of the JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->xTicks === null ? [] : [
            'ticks' => $this->xTicks->jsonSerialize(),
            'options' => $this->aOptions,
        ];
    }
}
