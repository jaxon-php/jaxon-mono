<?php

/**
 * Php.php - Jaxon config reader
 *
 * Read the config data from a PHP config file.
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2022 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Utils\Config\Reader;

use Jaxon\Utils\Config\Exception\FileAccess;
use Jaxon\Utils\Config\Exception\FileContent;

use function is_array;
use function realpath;
use function is_readable;

class PhpReader
{
    /**
     * Read options from a PHP config file
     *
     * @param string $sConfigFile The full path to the config file
     *
     * @return array
     * @throws FileAccess
     * @throws FileContent
     */
    public static function read(string $sConfigFile): array
    {
        $sConfigFile = realpath($sConfigFile);
        if(!is_readable($sConfigFile))
        {
            throw new FileAccess($sConfigFile);
        }
        $aConfigOptions = include($sConfigFile);
        if(!is_array($aConfigOptions))
        {
            throw new FileContent($sConfigFile);
        }

        return $aConfigOptions;
    }
}