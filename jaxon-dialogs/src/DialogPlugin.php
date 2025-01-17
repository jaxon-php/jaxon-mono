<?php

/**
 * DialogPlugin.php - modal, alert and confirm dialogs for Jaxon.
 *
 * Show modal, alert and confirm dialogs with various javascript libraries.
 * This class generates js ans css code for dialog libraries.
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2016 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Dialogs;

use Jaxon\App\Config\ConfigManager;
use Jaxon\App\I18n\Translator;
use Jaxon\Exception\SetupException;
use Jaxon\Plugin\AbstractPlugin;

use function array_reduce;
use function count;
use function json_encode;
use function trim;

class DialogPlugin extends AbstractPlugin
{
    /**
     * @const The plugin name
     */
    const NAME = 'dialog_code';

    /**
     * @var array
     */
    protected $aLibraries = null;

    /**
     * The constructor
     *
     * @param ConfigManager $xConfigManager
     * @param Translator $xTranslator
     * @param DialogManager $xDialogManager
     */
    public function __construct(private ConfigManager $xConfigManager,
        private Translator $xTranslator, private DialogManager $xDialogManager)
    {}

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getHash(): string
    {
        // The version number is used as hash
        return '5.0.0';
    }

    /**
     * @return array
     */
    private function getLibraries(): array
    {
        return $this->aLibraries ?: $this->aLibraries = $this->xDialogManager->getLibraries();
    }

    /**
     * @inheritDoc
     */
    public function getJs(): string
    {
        return array_reduce($this->getLibraries(), function($sCode, $xLibrary) {
            return $sCode . $xLibrary->getJs() . "\n\n";
        }, '');
    }

    /**
     * @inheritDoc
     */
    public function getCss(): string
    {
        return array_reduce($this->getLibraries(), function($sCode, $xLibrary) {
            return $sCode . trim($xLibrary->getCss()) . "\n\n";
        }, '');
    }

    /**
     * @inheritDoc
     * @throws SetupException
     */
    public function getScript(): string
    {
        return array_reduce($this->getLibraries(), function($sCode, $xLibrary) {
            return $sCode . trim($xLibrary->getScript()) . "\n\n";
        }, '');
    }

    /**
     * @return string
     */
    private function getOptionsJs(): string
    {
        $aOptions = [
            'labels' => $this->xTranslator->translations('labels'),
            'defaults' => $this->xConfigManager->getAppOption('dialogs.default', []),
        ];
        $aLibrariesOptions = [];
        foreach($this->getLibraries() as $xLibrary)
        {
            $aLibOptions = $xLibrary->helper()->getJsOptions();
            if(count($aLibOptions) > 0)
            {
                $aLibrariesOptions[$xLibrary->getName()] = $aLibOptions;
            }
        }
        if(count($aLibrariesOptions) > 0)
        {
            $aOptions['options'] = $aLibrariesOptions;
        }
        return "jaxon.dialog.config(" . json_encode($aOptions) . ");\n\n";
    }

    /**
     * @return string
     */
    private function _getReadyScript(): string
    {
        return array_reduce($this->getLibraries(), function($sCode, $xLibrary) {
            return $sCode . trim($xLibrary->getReadyScript()) . "\n\n";
        }, '');
    }

    /**
     * @inheritDoc
     */
    public function getReadyScript(): string
    {
        return $this->getOptionsJs() . $this->_getReadyScript();
    }
}
