<?php
namespace Xenolope\Silex\Provider;

use Symfony\Component\HttpFoundation\Request;
use Xenolope\Silex\BaseTestCase;

class PhpDiControllerResolverTest extends BaseTestCase
{

    public function testResolverInjectsOnServiceController()
    {
        $controller = new TestController();

        $this->app->get('/', array($controller, 'test'));
        $this->app->handle(Request::create('/'));

        $this->assertNotEmpty($controller->object);
        $this->assertInstanceOf('stdClass', $controller->object);
    }
}

class TestController
{

    /**
     * @inject
     * @var \stdClass
     */
    public $object;

    public function test()
    {
        return __METHOD__;
    }
}
