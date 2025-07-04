<?php

namespace Jaxon\Tests\TestRequestHandler;

use Jaxon\Jaxon;
use Jaxon\Exception\RequestException;
use Jaxon\Exception\SetupException;
use Jaxon\Request\Target;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

use function get_class;

class CallbackTest extends TestCase
{
    /**
     * @var Target
     */
    protected $xTarget;

    /**
     * @var mixed
     */
    protected $xCallable;

    /**
     * @var bool
     */
    protected $bEndRequest;

    /**
     * @var int
     */
    protected $nBootCount = 0;

    /**
     * @var string
     */
    protected $sMessage = '';

    /**
     * @throws SetupException
     */
    public function setUp(): void
    {
        jaxon()->setOption('core.response.send', false);
        jaxon()->callback()->boot(function() {
            $this->nBootCount++;
        });
        jaxon()->setOption('core.prefix.class', '');
        // A second callback
        jaxon()->callback()->boot(function() {
            $this->nBootCount += 2;
        });
        jaxon()->register(Jaxon::CALLABLE_FUNCTION, 'my_first_function',
            __DIR__ . '/../src/first.php');
        jaxon()->register(Jaxon::CALLABLE_DIR, __DIR__ . '/../src/response', ['autoload' => true]);
    }

    /**
     * @throws SetupException
     */
    public function tearDown(): void
    {
        jaxon()->reset();
        parent::tearDown();
    }

    public function testBootCallback()
    {
        $this->assertEquals(3, $this->nBootCount);
        // Too late to define a callback.
        jaxon()->callback()->boot(function() {
            $this->nBootCount++;
        });
        $this->assertEquals(3, $this->nBootCount);

        // Process the request and get the response.
        jaxon()->processRequest();

        // The callbacks has run now.
        $this->assertEquals(3, $this->nBootCount);
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testClassInitCallback()
    {
        $this->xCallable = null;
        jaxon()->callback()->init(function($xCallable) {
            $this->xCallable = clone $xCallable;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'simple',
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        jaxon()->processRequest();

        $this->assertNotNull($this->xCallable);
        $this->assertEquals('TestCb', get_class($this->xCallable));
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testFunctionAfterCallbackValidity()
    {
        $this->xTarget = null;
        jaxon()->callback()->after(function($xTarget) {
            $this->xTarget = clone $xTarget;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'func',
                        'name' => 'my_first_function',
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        $this->assertTrue(jaxon()->canProcessRequest());
        jaxon()->processRequest();

        $this->assertNotNull($this->xTarget);
        $this->assertFalse($this->xTarget->isClass());
        $this->assertTrue($this->xTarget->isFunction());
        $this->assertEquals('', $this->xTarget->getClassName());
        $this->assertEquals('', $this->xTarget->getMethodName());
        $this->assertEquals('my_first_function', $this->xTarget->getFunctionName());
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testClassBeforeCallbackValidity()
    {
        $this->xTarget = null;
        jaxon()->callback()->before(function($xTarget, &$bEndRequest) {
            $this->xTarget = clone $xTarget;
            $this->bEndRequest = $bEndRequest;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'simple',
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        $this->assertTrue(jaxon()->canProcessRequest());
        jaxon()->processRequest();

        $this->assertFalse($this->bEndRequest);
        $this->assertNotNull($this->xTarget);
        $this->assertTrue($this->xTarget->isClass());
        $this->assertFalse($this->xTarget->isFunction());
        $this->assertEquals('TestCb', $this->xTarget->getClassName());
        $this->assertEquals('simple', $this->xTarget->getMethodName());
        $this->assertEquals('', $this->xTarget->getFunctionName());
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testClassAfterCallbackValidity()
    {
        $this->xTarget = null;
        jaxon()->callback()->after(function($xTarget) {
            $this->xTarget = clone $xTarget;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'simple',
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        $this->assertTrue(jaxon()->canProcessRequest());
        jaxon()->processRequest();

        $this->assertNotNull($this->xTarget);
        $this->assertTrue($this->xTarget->isClass());
        $this->assertFalse($this->xTarget->isFunction());
        $this->assertEquals('TestCb', $this->xTarget->getClassName());
        $this->assertEquals('simple', $this->xTarget->getMethodName());
        $this->assertEquals('', $this->xTarget->getFunctionName());
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testBeforeCallbackSuccess()
    {
        $this->xTarget = null;
        jaxon()->callback()->before(function($xTarget, &$bEndRequest) {
            $xResponse = jaxon()->newResponse();
            $xResponse->alert('This is the before callback!');
            return $xResponse;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'simple',
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        jaxon()->processRequest();

        $xResponse = jaxon()->getResponse();
        $this->assertEquals(2, $xResponse->getCommandCount());
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testBeforeCallbackFailure()
    {
        $this->xTarget = null;
        jaxon()->callback()->before(function($xTarget, &$bEndRequest) {
            $bEndRequest = true;
            $xResponse = jaxon()->newResponse();
            $xResponse->alert('This is the before callback!');
            return $xResponse;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'simple',
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        jaxon()->processRequest();

        $xResponse = jaxon()->getResponse();
        // The Jaxon class is not called, so there is only one command in the response.
        $this->assertEquals(1, $xResponse->getCommandCount());
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testAfterCallbackResponse()
    {
        $this->xTarget = null;
        jaxon()->callback()->after(function($xTarget) {
            $xResponse = jaxon()->newResponse();
            $xResponse->alert('This is the after callback!');
            return $xResponse;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'simple',
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        jaxon()->processRequest();

        $xResponse = jaxon()->getResponse();
        $this->assertEquals(2, $xResponse->getCommandCount());
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testInvalidRequestCallback()
    {
        $this->sMessage = '';
        jaxon()->callback()->invalid(function(RequestException $e) {
            $this->sMessage = $e->getMessage();
            $xResponse = jaxon()->newResponse();
            $xResponse->alert('This is the invalid callback!');
            return $xResponse;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'sim ple', // There's an error in the function name.
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        $this->expectException(RequestException::class);
        jaxon()->processRequest();

        $xResponse = jaxon()->getResponse();
        $this->assertEquals(2, $xResponse->getCommandCount());
        $this->assertNotEquals('', $this->sMessage);
    }

    /**
     * @throws SetupException
     * @throws RequestException
     */
    public function testUserFunctionErrorCallback()
    {
        $this->sMessage = '';
        jaxon()->callback()->error(function(Exception $e) {
            $this->sMessage = $e->getMessage();
            $xResponse = jaxon()->newResponse();
            $xResponse->alert('This is the error callback!');
            return $xResponse;
        });
        // Send a request to the registered class
        jaxon()->di()->set(ServerRequestInterface::class, function($c) {
            return $c->g(ServerRequestCreator::class)
                ->fromGlobals()
                ->withParsedBody([
                    'jxncall' => json_encode([
                        'type' => 'class',
                        'name' => 'TestCb',
                        'method' => 'error', // This function throws an exception.
                        'args' => [],
                    ]),
                ])
                ->withMethod('POST');
        });
        // Process the request and get the response
        $this->expectException(Exception::class);
        jaxon()->processRequest();

        $xResponse = jaxon()->getResponse();
        $this->assertEquals(2, $xResponse->getCommandCount());
        $this->assertNotEquals('', $this->sMessage);
    }
}
