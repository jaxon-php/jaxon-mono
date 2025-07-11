<?php

/**
 * ComponentHelper.php
 *
 * Provides helper functions to components.
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2022 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Plugin\Request\CallableClass;

use Jaxon\App\Session\SessionInterface;
use Jaxon\App\Stash\Stash;
use Jaxon\App\View\ViewRenderer;
use Jaxon\Di\ComponentContainer;
use Jaxon\Exception\SetupException;
use Jaxon\Script\CallFactory;
use Jaxon\Script\Call\JxnCall;
use Jaxon\Request\Target;
use Jaxon\Request\Upload\UploadHandlerInterface;
use Psr\Log\LoggerInterface;

use function trim;

class ComponentHelper
{
    /**
     * @var Target
     */
    public $xTarget;

    /**
     * The constructor
     *
     * @param ComponentContainer $cdi
     * @param JxnCall $xJxnCall
     * @param CallFactory $xFactory
     * @param ViewRenderer $xViewRenderer
     * @param LoggerInterface $xLogger
     * @param SessionInterface $xSessionManager
     * @param UploadHandlerInterface|null $xUploadHandler
     *
     * @throws SetupException
     */
    public function __construct(public ComponentContainer $cdi, public JxnCall $xJxnCall,
        public CallFactory $xFactory, public ViewRenderer $xViewRenderer,
        public LoggerInterface $xLogger, public ?SessionInterface $xSessionManager,
        public Stash $xStash, public ?UploadHandlerInterface $xUploadHandler)
    {}

    /**
     * Get an instance of a Jaxon class by name
     *
     * @template T
     * @param class-string<T> $sClassName the class name
     *
     * @return T|null
     * @throws SetupException
     */
    public function cl(string $sClassName): mixed
    {
        return $this->cdi->makeComponent($sClassName);
    }

    /**
     * Get the js call factory.
     *
     * @param string $sClassName
     *
     * @return JxnCall
     */
    public function rq(string $sClassName = ''): JxnCall
    {
        $sClassName = trim($sClassName);
        return !$sClassName ? $this->xJxnCall : $this->xFactory->rq($sClassName);
    }
}
