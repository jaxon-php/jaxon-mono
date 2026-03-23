<?php

/**
 * Card.php
 *
 * A card containing one or more graphs.
 *
 * @package jaxon-charts
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2026 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-charts
 */

namespace Jaxon\Charts\Chart;

use Jaxon\Charts\Chart\Option\OptionTrait;
use JsonSerializable;

use function count;
use function trim;

class Card implements JsonSerializable
{
    use OptionTrait;

    /**
     * The HTML element selector
     *
     * @var string
     */
    protected string $sSelector;

    /**
     * The graph charts
     *
     * @var array<Graph>
     */
    protected array $aGraphs = [];

    /**
     * The pie charts
     *
     * @var array<Pie>
     */
    protected array $aPies = [];

    /**
     * The card width
     *
     * @var string
     */
    protected string $sWidth = '';

    /**
     * The card height
     *
     * @var string
     */
    protected string $sHeight = '';

    /**
     * The card X axes
     *
     * @var array<Axis>
     */
    protected array $aAxesX = [];

    /**
     * The card Y axes
     *
     * @var array<Axis>
     */
    protected array $aAxesY = [];

    /**
     * The constructor.
     *
     * @param string $sSelector The jQuery selector
     */
    public function __construct(string $sSelector)
    {
        $this->sSelector = trim($sSelector, " \t");
    }

    /**
     * Set the container width.
     *
     * @param string $sWidth The container width
     *
     * @return static
     */
    public function width(string $sWidth): static
    {
        $this->sWidth = trim($sWidth, " \t");
        return $this;
    }

    /**
     * Set the container height.
     *
     * @param string $sHeight The container height
     *
     * @return static
     */
    public function height(string $sHeight): static
    {
        $this->sHeight = trim($sHeight, " \t");
        return $this;
    }

    /**
     * Add a pie to the card.
     *
     * @param array $aOptions
     *
     * @return Pie
     */
    public function pie(array $aOptions = []): Pie
    {
        $xPie = new Pie($aOptions);
        $this->aPies[] = $xPie;
        return $xPie;
    }

    /**
     * Add a new graph to the card.
     *
     * @param array $aOptions
     *
     * @return Graph
     */
    public function graph(array $aOptions = []): Graph
    {
        $xGraph = new Graph($aOptions);
        $this->aGraphs[] = $xGraph;
        return $xGraph;
    }

    /**
     * Add an X axis to the card.
     *
     * @param array $aOptions
     *
     * @return Axis
     */
    public function xaxes(array $aOptions = []): Axis
    {
        $xAxis = new Axis($aOptions);
        $this->aAxesX[] = $xAxis;
        return $xAxis;
    }

    /**
     * Add a Y axis to the card.
     *
     * @param array $aOptions
     *
     * @return Axis
     */
    public function yaxes(array $aOptions = []): Axis
    {
        $xAxis = new Axis($aOptions);
        $this->aAxesY[] = $xAxis;
        return $xAxis;
    }

    /**
     * Convert this object to an array for json format.
     *
     * This is a method of the JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $aJson = [
            'selector' => $this->sSelector,
            'size' => ['width' => $this->sWidth, 'height' => $this->sHeight],
            'options' => $this->aOptions,
        ];

        if(count($this->aPies) > 0)
        {
            $aJson['pies'] = $this->aPies;
            return $aJson;
        }

        $aJson['graphs'] = $this->aGraphs;
        if(count($this->aAxesX) > 0)
        {
            $aJson['xaxes'] = $this->aAxesX;
        }
        if(count($this->aAxesY) > 0)
        {
            $aJson['yaxes'] = $this->aAxesY;
        }

        return $aJson;
    }
}
