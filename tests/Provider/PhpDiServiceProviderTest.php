<?php
namespace Xenolope\Silex\Provider;

use Silex\Provider\SessionServiceProvider;
use Xenolope\Silex\BaseTestCase;

/**
 * Class PhpDiServiceProviderTest
 * @package Xenolope\Silex\Provider
 *
 * Based on thispagecannotbefound/silex-php-di, by Abel de Beer, released under the MIT license
 * @link https://github.com/thispagecannotbefound/SilexPhpDi
 */
class PhpDiServiceProviderTest extends BaseTestCase
{

    public function testCompositeContainerUsesPimpleAsFallback()
    {
        $value = __LINE__;

        $this->app[__METHOD__] = $value;

        $result = $this->app['di']->get(__METHOD__);

        $this->assertEquals($value, $result);
    }

    public function testCompositeContainerFavoursPhpDiOverPimple()
    {
        $pimpleValue = __LINE__;
        $phpDiValue = __LINE__;

        $this->app[__METHOD__] = $pimpleValue;
        $this->app['di.raw']->set(__METHOD__, $phpDiValue);

        $result = $this->app['di']->get(__METHOD__);

        $this->assertEquals($phpDiValue, $result);
    }

    public function testGetSilexApplicationReturnsApp()
    {
        $result = $this->app['di']->get('Silex\Application');

        $this->assertSame($this->app, $result);
    }

    public function testAliasesGetSilexServices()
    {
        $this->app->register(new SessionServiceProvider());
        $this->app['session.test'] = true;

        $result = $this->app['di']->get('Symfony\Component\HttpFoundation\Session\SessionInterface');

        $this->assertSame($this->app['session'], $result);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\SessionInterface', $result);
    }
}
