<?php

namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class EtdSeoOptimizerPlugin
 * @package Grav\Plugin
 */
class EtdSeoOptimizerPlugin extends Plugin {

    /**
     * RegExp to validate css class name
     */
    const CSS_CLASS_REGEXP = '/-?[_a-zA-Z]+[_a-zA-Z0-9-]*/';

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents() {

        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized() {

        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onPageContentRaw' => ['onPageContentRaw', 0]
        ]);
    }

    /**
     * Do some work for this event, full details of events can be found
     * on the learn site: http://learn.getgrav.org/plugins/event-hooks
     *
     * @param Event $e
     */
    public function onPageContentRaw(Event $e) {

        // Get a variable from the plugin configuration
        $span_class = $this->grav['config']->get('plugins.etd-seo-optimizer.span_class');

        // Filter to have a valid css class
        if (!preg_match(self::CSS_CLASS_REGEXP, $span_class, $m)) {
            return;
        }

        // Get the current raw content
        $content = $e['page']->getRawContent();

        // Replace special tags
        $content = str_replace(['[SEO]', '[/SEO]'], ['<span class="' . htmlspecialchars($m[0]) . '">', '</span>'], $content);

        // Set the output with the special tags replaced
        $e['page']->setRawContent($content);
    }
}