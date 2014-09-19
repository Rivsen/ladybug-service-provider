<?php

namespace Rswork\Silex\Provider;

use Ladybug\Dumper;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Rswork\Silex\Extension\LadybugExtension;
use Rswork\Silex\DataCollector\LadybugDataCollector;

class LadybugServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['ladybug.options'] = array(
            'theme' => 'modern',
            'expanded' => false,
            'silenced' => false,
            'array_max_nesting_level' => 9,
            'object_max_nesting_level' => 3,
        );

        $app['ladybug.dumper'] = $app->share(function () use ($app) {
            $dumper = new Dumper();
            $dumper->setOptions( $app['ladybug.options'] );

            return $dumper;
        });

        $app['ladybug'] = $app->share(function() use ($app){
            return new LadybugDataCollector( $app['ladybug.dumper'] );
        });

        $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig, $app) {
            $twig->addExtension(new LadybugExtension($app));

            return $twig;
        }));

        if( isset( $app['profiler'] ) ) {
            $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {
                $loader->addPath($app['rswork.templates_path'], 'Rswork');
                return $loader;
            }));

            $app['rswork.templates_path'] = function () {
                return realpath(__DIR__.'/../../views');
            };

            $app['data_collector.templates'] = array_merge(
                $app['data_collector.templates'],
                array(array('ladybug', '@Rswork/collector/ladybug.html.twig'))
            );

            $app['data_collectors'] = $app->share( $app->extend( 'data_collectors', function( $collectors, $app ){
                $collectors['ladubug'] = $app->share(function( $app ){
                    return $app['ladybug'];
                });

                return $collectors;
            } ) );
        }
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}
