<?php
/**
 * ImgSrcset Plugin
 *
 * PHP version 7
 *
 * @category   Extensions
 * @package    Grav
 * @subpackage ImgSrcset
 * @author     Ole Vik <git@olevik.net>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       https://github.com/OleVik/grav-plugin-imgsrcset
 */
namespace Grav\Plugin;

use Grav\Common\Data;
use Grav\Common\Plugin;
use Grav\Common\Grav;
use Grav\Common\Page\Page;
use RocketTheme\Toolbox\Event\Event;
use PHPHtmlParser\Dom;

/**
 * Adds a srcset-attribute to img-elements to allow for responsive images in Markdown
 *
 * Class ImgSrcsetPlugin
 *
 * @category Extensions
 * @package  Grav\Plugin
 * @author   Ole Vik <git@olevik.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/OleVik/grav-plugin-imgsrcset
 */
class ImgSrcsetPlugin extends Plugin
{

    /**
     * Register events with Grav
     *
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
     *
     * @return void
     */
    public function onPageContentProcessed()
    {
        if ($this->isAdmin()) {
            return;
        }
        $config = (array) $this->config->get('plugins.imgsrcset');
        $page = $this->grav['page'];
        $config = $this->mergeConfig($page);
        if ($config['enabled']) {
            include __DIR__ . '/vendor/autoload.php';
            $dom = new Dom;
            $dom->load($page->getRawContent());
            $images = $dom->find('img');
            $widths = $config['widths'];
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
                $image->setAttribute('sizes', $config['sizes']);
            }
            $page->setRawContent($dom->outerHtml);
        }
    }
}
