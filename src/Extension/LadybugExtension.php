<?php

namespace Rswork\Silex\Extension;

/**
 * Twig extension for the bundle.
 */
class LadybugExtension extends \Twig_Extension
{

    private $app;

    public function __construct(\Pimple $app)
    {
        $this->app = $app;
    }

    /**
     * Getter.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'ladybug_dump' => new \Twig_Filter_Method($this, 'ladybug_dump', array('is_safe' => array('html'))),
            'ld'  => new \Twig_Filter_Method($this, 'ladybug_dump', array('is_safe' => array('html')))
        );
    }

    /**
     * Getter.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'ladybug_dump' => new \Twig_Function_Method($this, 'ladybug_dump', array('is_safe' => array('html'))),
            'ld'  => new \Twig_Function_Method($this, 'ladybug_dump', array('is_safe' => array('html')))
        );
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function ladybug_dump()
    {
        $ladybug = $this->app['ladybug.dumper'];
        $html = call_user_func_array(array($ladybug, 'dump'), func_get_args());

        return $html;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ladybug_extension';
    }
}
