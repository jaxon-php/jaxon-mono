<?php

/**
 * JsCode.php
 *
 * Additional javascript codes generated by a Jaxon plugin.
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2025 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Plugin\Code;

class JsCode
{
    /**
     * The main javascript code
     *
     * @var string
     */
    public $sJs = '';

    /**
     * The js files
     *
     * @var array
     */
    public $aFiles = [];

    /**
     * The javascript code to be inserted inline before the main code
     *
     * @var string
     */
    public $sJsBefore = '';

    /**
     * The javascript code to be inserted inline after the main code
     *
     * @var string
     */
    public $sJsAfter = '';
}
