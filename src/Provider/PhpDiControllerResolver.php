<?php
namespace Xenolope\Silex\Provider;

use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * Class PhpDiControllerResolver
 * @package Xenolope\Silex\Provider
 *
 * Based on thispagecannotbefound/silex-php-di, by Abel de Beer, released under the MIT license
 * @link https://github.com/thispagecannotbefound/SilexPhpDi
 */
class PhpDiControllerResolver implements ControllerResolverInterface
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ControllerResolverInterface
     */
    protected $controllerResolver;

    public function __construct(Container $container, ControllerResolverInterface $controllerResolver)
    {
        $this->container = $container;
        $this->controllerResolver = $controllerResolver;
    }

    /**
     * Returns the Controller instance associated with a Request.
     *
     * As several resolvers can exist for a single application, a resolver must
     * return false when it is not able to determine the controller.
     *
     * The resolver must only throw an exception when it should be able to load
     * controller but cannot because of some errors made by the developer.
     *
     * @param Request $request A Request instance
     *
     * @return callable|false A PHP callable representing the Controller,
     *                        or false if this resolver is not able to determine the controller
     *
     * @throws \LogicException If the controller can't be found
     *
     * @api
     */
    public function getController(Request $request)
    {
        $controller = $request->attributes->get('_controller', null);

        if (is_array($controller)) {
            return $controller;
        }

        if (1 === substr_count($controller, ':')) {
            list($class, $method) = explode(':', $controller, 2);

            return array($this->container->make($class), $method);
        }

        $controller = $this->controllerResolver->getController($request);

        if (!$controller instanceof \Closure) {
            $instance = is_array($controller) ? reset($controller) : $controller;
            if (is_object($instance)) {
                $this->container->injectOn($instance);
            }
        }

        return $controller;
    }

    /**
     * Returns the arguments to pass to the controller.
     *
     * @param Request $request A Request instance
     * @param callable $controller A PHP callable
     *
     * @return array An array of arguments to pass to the controller
     *
     * @throws \RuntimeException When value for argument given is not provided
     *
     * @api
     */
    public function getArguments(Request $request, $controller)
    {
        return $this->controllerResolver->getArguments($request, $controller);
    }
}
