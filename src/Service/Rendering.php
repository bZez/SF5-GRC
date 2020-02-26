<?php

namespace App\Service;

use Twig\Environment;

class Rendering
{
    public function __construct(Environment $environment)
    {
        $this->twig = $environment;
    }

   public function block($template, $block, $params = null)
    {
        /** @var Environment $twig */
        $twig = $this->twig;
        $template = $twig->load($template);
        if(!$params) {
            $params = array();
        }

        return $template->renderBlock($block, $twig->mergeGlobals($params));

    }
}