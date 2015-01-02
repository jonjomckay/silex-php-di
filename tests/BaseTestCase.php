<?php
namespace Xenolope\Silex;

use Silex\Application;
use Xenolope\Silex\Provider\PhpDiServiceProvider;

/**
 * Class BaseTestCase
 * @package Xenolope\Silex
 *
 * Based on thispagecannotbefound/silex-php-di, by Abel de Beer, released under the MIT license
 * @link https://github.com/thispagecannotbefound/SilexPhpDi
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Application
     */
    protected $app;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register(new PhpDiServiceProvider());

        $this->app['debug'] = true;
        $this->app['exception_handler']->disable();
    }
}
