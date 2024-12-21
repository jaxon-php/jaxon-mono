<?php

/**
 * ResponseManager.php - Jaxon Response Manager
 *
 * This class stores and tracks the response that will be returned after processing a request.
 * The Response Manager represents a single point of contact for working with <AbstractResponsePlugin> objects.
 *
 * @package jaxon-core
 * @author Jared White
 * @author J. Max Wilson
 * @author Joseph Woolley
 * @author Steffen Konerow
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
 * @copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
 * @copyright 2016 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Response;

use Jaxon\App\Dialog\DialogManager;
use Jaxon\App\I18n\Translator;
use Jaxon\Exception\AppException;
use Jaxon\Di\Container;
use Jaxon\Script\JxnCall;
use Closure;
use JsonSerializable;

use function array_filter;
use function array_merge;
use function count;
use function trim;

class ResponseManager
{
    /**
     * @var Container
     */
    private $di;

    /**
     * @var DialogManager
     */
    protected $xDialogManager;

    /**
     * @var Translator
     */
    protected $xTranslator;

    /**
     * @var string
     */
    private $sCharacterEncoding;

    /**
     * The current response object that will be sent back to the browser
     * once the request processing phase is complete
     *
     * @var AbstractResponse
     */
    private $xResponse = null;

    /**
     * The error message
     *
     * @var string
     */
    private $sErrorMessage = '';

    /**
     * The debug messages
     *
     * @var array
     */
    private $aDebugMessages = [];

    /**
     * The commands that will be sent to the browser in the response
     *
     * @var array
     */
    protected $aCommands = [];

    /**
     * If the commands beeing added are to be confirmed
     *
     * @var bool
     */
    private $bOnConfirm = false;

    /**
     * The commands that will be sent to the browser in the response
     *
     * @var array
     */
    private $aConfirmCommands = [];

    /**
     * @param Container $di
     * @param Translator $xTranslator
     * @param DialogManager $xDialogManager
     * @param string $sCharacterEncoding
     */
    public function __construct(Container $di, Translator $xTranslator,
        DialogManager $xDialogManager, string $sCharacterEncoding)
    {
        $this->di = $di;
        $this->xTranslator = $xTranslator;
        $this->xDialogManager = $xDialogManager;
        $this->sCharacterEncoding = $sCharacterEncoding;
    }

    /**
     * @return DialogManager
     */
    public function dialog(): DialogManager
    {
        return $this->xDialogManager;
    }

    /**
     * Convert to string
     *
     * @param mixed $xData
     *
     * @return string
     */
    protected function str($xData): string
    {
        return trim((string)$xData, " \t\n");
    }

    /**
     * Get a translated string
     *
     * @param string $sText The key of the translated string
     * @param array $aPlaceHolders The placeholders of the translated string
     *
     * @return string
     */
    public function trans(string $sText, array $aPlaceHolders = []): string
    {
        return $this->xTranslator->trans($sText, $aPlaceHolders);
    }

    /**
     * Set the error message
     *
     * @param string $sErrorMessage
     *
     * @return void
     */
    public function setErrorMessage(string $sErrorMessage)
    {
        $this->sErrorMessage = $sErrorMessage;
    }

    /**
     * Get the error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->sErrorMessage;
    }

    /**
     * Get the commands in the response
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->aCommands;
    }

    /**
     * Get the number of commands in the response
     *
     * @return int
     */
    public function getCommandCount(): int
    {
        return count($this->aCommands);
    }

    /**
     * Clear all the commands already added to the response
     *
     * @return void
     */
    public function clearCommands()
    {
        $this->aCommands = [];
    }

    /**
     * @param array|JsonSerializable $aArgs    The command arguments
     * @param bool $bRemoveEmpty    If true, remove empty arguments
     *
     * @return array
     */
    private function getCommandArgs(array|JsonSerializable $aArgs, bool $bRemoveEmpty = false): array
    {
        if(!$bRemoveEmpty)
        {
            return $aArgs;
        }
        return array_filter($aArgs, function($xArg) {
            return !empty($xArg);
        });
    }

    /**
     * Add a response command to the array of commands
     *
     * @param string $sName    The command name
     * @param array|JsonSerializable $aArgs    The command arguments
     * @param bool $bRemoveEmpty
     *
     * @return Command
     */
    public function addCommand(string $sName, array|JsonSerializable $aArgs,
        bool $bRemoveEmpty = false): Command
    {
        $xCommand = new Command([
            'name' => $this->str($sName),
            'args' => $this->getCommandArgs($aArgs, $bRemoveEmpty),
        ]);
        if($this->bOnConfirm)
        {
            $this->aConfirmCommands[] = $xCommand;
        }
        else
        {
            $this->aCommands[] = $xCommand;
        }
        return $xCommand;
    }

    /**
     * Response command that prompts user with [ok] [cancel] style message box
     *
     * The provided closure will be called with a response object as unique parameter.
     * If the user clicks cancel, the response commands defined in the closure will be skipped.
     *
     * @param Closure $fConfirm  A closure that defines the commands that can be skipped
     * @param array $aQuestion   The question to ask to the user
     *
     * @throws AppException
     *
     * @return self
     */
    public function addConfirmCommand(Closure $fConfirm, array $aQuestion): self
    {
        if($this->bOnConfirm)
        {
            throw new AppException($this->xTranslator->trans('errors.app.confirm.nested'));
        }
        $this->bOnConfirm = true;
        $fConfirm();
        $this->bOnConfirm = false;
        if(($nCommandCount = count($this->aConfirmCommands)) > 0)
        {
            // The confirm command must be inserted before the commands to be confirmed.
            $this->addCommand('script.confirm', [
                'count' => $nCommandCount,
                'question' => $aQuestion,
            ]);
            $this->aCommands = array_merge($this->aCommands, $this->aConfirmCommands);
            $this->aConfirmCommands = [];
        }
        return $this;
    }

    /**
     * Get the response to the Jaxon request
     *
     * @param AbstractResponse $xResponse
     *
     * @return void
     */
    public function setResponse(AbstractResponse $xResponse)
    {
        $this->xResponse = $xResponse;
    }

    /**
     * Get the response to the Jaxon request
     *
     * @return AbstractResponse
     */
    public function getResponse()
    {
        return $this->xResponse ?: $this->di->getResponse();
    }

    /**
     * Create a new Jaxon response
     *
     * @return Response
     */
    public function newResponse(): Response
    {
        return $this->di->newResponse();
    }

    /**
     * Create a new reponse for a Jaxon component
     *
     * @param JxnCall $xJxnCall
     *
     * @return ComponentResponse
     */
    public function newComponentResponse(JxnCall $xJxnCall): ComponentResponse
    {
        return $this->di->newComponentResponse($xJxnCall);
    }

    /**
     * Appends a debug message on the end of the debug message queue
     *
     * Debug messages will be sent to the client with the normal response
     * (if the response object supports the sending of debug messages, see: <AbstractResponsePlugin>)
     *
     * @param string $sMessage    The debug message
     *
     * @return void
     */
    public function debug(string $sMessage)
    {
        $this->aDebugMessages[] = $sMessage;
    }

    /**
     * Clear the response and appends a debug message on the end of the debug message queue
     *
     * @param string $sMessage The debug message
     *
     * @return void
     */
    public function error(string $sMessage)
    {
        $this->clearCommands();
        $this->debug($sMessage);
    }

    /**
     * Prints the debug messages into the current response object
     *
     * @return void
     */
    public function printDebug()
    {
        foreach($this->aDebugMessages as $sMessage)
        {
            $this->addCommand('script.debug', ['message' => $this->str($sMessage)]);
        }
        // $this->aDebugMessages = [];
    }

    /**
     * Get the content type of the HTTP response
     *
     * @return string
     */
    public function getContentType(): string
    {
        return empty($this->sCharacterEncoding) ? $this->getResponse()->getContentType() :
            $this->getResponse()->getContentType() . '; charset="' . $this->sCharacterEncoding . '"';
    }

    /**
     * Get the JSON output of the response
     *
     * @return string
     */
    public function getOutput(): string
    {
        return $this->getResponse()->getOutput();
    }

    /**
     * Get the debug messages
     *
     * @return array
     */
    public function getDebugMessages(): array
    {
        return $this->aDebugMessages;
    }
}
