<?php
namespace Xenolope\Silex\Provider;

use Acclimate\Container\CompositeContainer;
use Acclimate\Container\ContainerAcclimator;
use DI\ContainerBuilder;
use Silex\Application;
use Silex\ServiceProviderInterface;

class PhpDiServiceProvider implements ServiceProviderInterface
{

    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['di.definitions'] = array();
        $app['di.options'] = array();
        $app['di.default_options'] = array(
            'cache' => null,
            'containerClass' => 'DI\Container',
            'useAnnotations' => true,
            'useAutowiring' => true,
            'writeProxiesToFile' => false,
            'proxyDirectory' => null,
            'silexAliases' => true,
            'injectOnControllers' => true
        );

        $app['di'] = $app->share(function() use ($app) {
            $acclimator = new ContainerAcclimator();
            $container = new CompositeContainer();

            /** @var ContainerBuilder $builder */
            $builder = $app['di.builder'];
            $builder->wrapContainer($container);

            $phpDi = $builder->build();
            $phpDi->set('Silex\Application', $app);

            $container->addContainer($acclimator->acclimate($phpDi));
            $container->addContainer($acclimator->acclimate($app));

            return $container;
        });

        $app['di.builder'] = $app->share(function() use ($app) {
            $options = $app['di.options_merged'];

            $builder = new ContainerBuilder($options['containerClass']);
            $builder->useAnnotations((bool) $options['useAnnotations']);
            $builder->useAutowiring((bool) $options['useAutowiring']);
            $builder->writeProxiesToFile($options['writeProxiesToFile'], $options['proxyDirectory']);

            if ($options['cache']) {
                $builder->setDefinitionCache($options['cache']);
            }

            $definitions = (array) $app['di.definitions'];

            if ($options['silexAliases']) {
                $definitions[] = __DIR__  . '/config.php';
            }

            foreach ($definitions as $file) {
                $builder->addDefinitions($file);
            }

            return $builder;
        });

        $app['di.raw'] = $app->share(function() use ($app) {
            return $app['di']->get($app['di.options_merged']['containerClass']);
        });

        $app['di.options_merged'] = $app->share(function() use ($app) {
            return array_merge($app['di.default_options'], $app['di.options']);
        });

        $app['resolver'] = $app->share($app->extend('resolver', function ($resolver, $app) {
            if ($app['di.options_merged']['injectOnControllers']) {
                return new PhpDiControllerResolver($app['di.raw'], $resolver);
            }

            return $resolver;
        }));
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {

    }
}
