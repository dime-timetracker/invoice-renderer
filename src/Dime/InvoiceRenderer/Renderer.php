<?php
namespace Dime\InvoiceRenderer;

use \Twig_Environment;
use \Twig_Loader_Filesystem;
use \mikehaertl\wkhtmlto\Pdf;

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
        if (!$args['increment_no']) {
            throw new IncrementNoNotSetException('Please define an increment no!');
        }
        $loader = new Twig_Loader_Filesystem(dirname($this->template));
        $twig = new Twig_Environment($loader);
        return $twig->render(basename($this->template), $args);
    }

    public function pdf ($args)
    {
        $pdf = new Pdf($this->html($args));
        $pdf->send('invoice-' . str_replace('/[^0-9\-_]/', '', $args['increment_no']) . '.pdf');
    }
}
