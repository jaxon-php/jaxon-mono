<?php

namespace Jaxon\Di\Traits;

use Jaxon\App\Stash\Stash;
use Jaxon\Utils\Config\ConfigReader;
use Jaxon\Utils\Http\UriDetector;
use Jaxon\Utils\Template\TemplateEngine;

use function rtrim;
use function trim;

trait UtilTrait
{
    /**
     * Register the values into the container
     *
     * @return void
     */
    private function registerUtils()
    {
        // Config reader
        $this->set(ConfigReader::class, function() {
            return new ConfigReader();
        });
        // Template engine
        $this->set(TemplateEngine::class, function($di) {
            $xTemplateEngine = new TemplateEngine();
            $sTemplateDir = rtrim(trim($di->g('jaxon.core.dir.template')), '/\\');
            $sPaginationDir = $sTemplateDir . DIRECTORY_SEPARATOR . 'pagination';
            $xTemplateEngine->addNamespace('jaxon', $sTemplateDir, '.php');
            $xTemplateEngine->addNamespace('pagination', $sPaginationDir, '.php');
            $xTemplateEngine->setDefaultNamespace('jaxon');
            return $xTemplateEngine;
        });
        // URI detector
        $this->set(UriDetector::class, function() {
            return new UriDetector();
        });

        // Temp cache for Jaxon callable classes
        $this->set(Stash::class, function() {
            return new Stash();
        });
    }

    /**
     * Get the template engine
     *
     * @return TemplateEngine
     */
    public function getTemplateEngine(): TemplateEngine
    {
        return $this->g(TemplateEngine::class);
    }

    /**
     * Get the temp cache for Jaxon callable classes
     *
     * @return Stash
     */
    public function getStash(): Stash
    {
        return $this->g(Stash::class);
    }
}
