<?php
namespace Xenolope\Silex\Provider;

use Symfony\Component\HttpFoundation\Request;
use Xenolope\Silex\BaseTestCase;

/**
 * Class PhpDiControllerResolverTest
 * @package Xenolope\Silex\Provider
 *
 * Based on thispagecannotbefound/silex-php-di, by Abel de Beer, released under the MIT license
 * @link https://github.com/thispagecannotbefound/SilexPhpDi
 */
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
