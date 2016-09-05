<?php
namespace Dime\InvoiceRenderer;

use \Twig_Environment;
use \Twig_Loader_Filesystem;

class Renderer
{
    protected $template;

    /**
     * set path to the template
     *
     * @param string $template Template path
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Render HTML template with the given arguments
     *
     * @param array $args Arguments to be rendered in the template
     * @return string
     */
    public function html ($args)
    {
        if (is_null($this->template)) {
            throw new TemplateNotSetException('Please set the template before rendering!');
        }
        $loader = new Twig_Loader_Filesystem(dirname($this->template));
        $twig = new Twig_Environment($loader);
        return $twig->render(basename($this->template), $args);
    }
}
