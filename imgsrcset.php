<?php
namespace Grav\Plugin;

use Grav\Common\Data;
use Grav\Common\Plugin;
use Grav\Common\Grav;
use Grav\Common\Page\Page;
use RocketTheme\Toolbox\Event\Event;

require __DIR__ . '/vendor/autoload.php';
use PHPHtmlParser\Dom;

/**
 * Adds a srcset-attribute to img-elements to allow for responsive images in Markdown
 *
 * Class ImgSrcsetPlugin
 * @package Grav\Plugin
 * @return void
 * @license MIT License by Ole Vik
 */
class ImgSrcsetPlugin extends Plugin
{

    /**
     * Register events with Grav
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPageContentProcessed' => ['onPageContentProcessed', 0]
        ];
    }

    /**
     * Iterates over images in page content and rewrites paths
     * @return void
     */
    public function onPageContentProcessed()
    {
        /* Check if Admin-interface */
        if ($this->isAdmin()) {
            return;
        }

        $config = (array) $this->config->get('plugins');
        $page = $this->grav['page'];
        if (isset($config['imgsrcset'])) {
            if ($config['imgsrcset']['enabled']) {
                $dom = new Dom;
                $dom->load($page->content());
                $images = $dom->find('img');
                $widths = $config['imgsrcset']['widths'];
                foreach ($images as $image) {
                    $file = pathinfo($image->getAttribute('src'));
                    $dirname = $file['dirname'];
                    $filename = $file['filename'];
                    $extension = $file['extension'];
                    $srcsets = '';
                    foreach ($widths as $width) {
                        $srcsets .= $dirname.'/'.$filename.'-'.$width.'.'.$extension.' '.$width.'w, ';
                    }
                    $srcsets = rtrim($srcsets, ", ");
                    $image->setAttribute('srcset', $srcsets);
                    $image->setAttribute('sizes', $config['imgsrcset']['sizes']);
                }
                $page->setRawContent($dom);
            }
        }
    }
}
