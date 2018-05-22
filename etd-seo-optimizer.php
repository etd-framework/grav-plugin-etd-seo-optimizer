<?php

namespace Grav\Plugin;

use Grav\Common\Page\Collection;
use Grav\Common\Page\Page;
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
            'onPageContentRaw'      => ['onPageContentRaw', 0],
            'onCollectionProcessed' => ['onCollectionProcessed', 0]
        ]);
    }

    /**
     * @param Event $e
     */
    public function onPageContentRaw(Event $e) {

        $this->modifyPage($e['page']);
    }

    /**
     * @param Event $e
     */
    public function onCollectionProcessed(Event $e) {

        /**
         * @var Collection $collection;
         */
        $collection = $e['collection'];

        if ($collection->count() > 0) {
            foreach ($collection as $page) {
                $this->modifyPage($page);
            }
        }
    }

    /**
     * @param Page $page
     */
    protected function modifyPage(Page $page) {

        // Get span class from the plugin configuration
        $span_class = $this->grav['config']->get('plugins.etd-seo-optimizer.span_class');

        // Filter to have a valid css class
        if (!preg_match(self::CSS_CLASS_REGEXP, $span_class, $m)) {
            return;
        }

        // Replace special tags in page header
        $header = $page->header();
        if (isset($header)) {
            foreach ($header as $k => $v) {
                $page->modifyHeader($k, $this->replace($v, $m[0]));
            }
        }

        // Get the current raw content
        $content = $page->getRawContent();

        // Set the page content with the special tags replaced if content is set
        if (isset($content)) {
            $content = $this->replace($content, $m[0]);
            $page->setRawContent($content);
        }

    }

    protected function replace($str, $span_class) {

        return str_replace(['[SEO]', '[/SEO]'], ['<span class="' . htmlspecialchars($span_class) . '">', '</span>'], $str);

    }
}
