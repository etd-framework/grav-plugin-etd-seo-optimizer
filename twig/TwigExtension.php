<?php

use Grav\Common\Grav;

class EtdSeoOptimizerPluginTwigExtension extends \Twig_Extension {

    const BREAKPOINTS = ["576px", "768px", "992px", "1200px"];

    /**
     * @var Grav
     */
    protected $grav;

    public function __construct() {

        $this->grav = Grav::instance();
    }

    public function getFunctions() {

        return [
            new \Twig_SimpleFunction('bgImgResponsive', [$this, 'bgImgResponsive'])
        ];
    }

    public function bgImgResponsive(\Grav\Common\Page\Medium\ImageMedium $image, $css_selector, $min_width = 480, $max_width = 1921, $step = 360) {

        // On crée les alternatives responsives.
        $image->derivatives($min_width, $max_width, $step);

        // On récupère les urls des images créées.
        $alternatives = explode(", ", $image->srcset(false));

        // Image de base
        $css = $css_selector . " { background-image: url('" . explode(" ", $alternatives[0])[0] . "'); }\n";

        // On retire l'image de base
        array_shift($alternatives);

        for ($i = 0; $i < count(self::BREAKPOINTS); $i++) {
            $css .= "@media (min-width: " . self::BREAKPOINTS[$i] . ") { " . $css_selector . " { background-image: url('" . explode(" ", $alternatives[$i])[0] . "'); } }\n";
        }

        $this->grav["assets"]->addInlineCss($css);

    }

}
