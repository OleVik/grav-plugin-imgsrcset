<?php
namespace Grav\Plugin;

use Grav\Common\Data;
use Grav\Common\Plugin;
use Grav\Common\Grav;
use Grav\Common\Page\Page;
use RocketTheme\Toolbox\Event\Event;
require __DIR__ . '/vendor/autoload.php';
use PHPHtmlParser\Dom;

class ImgSrcsetPlugin extends Plugin
{
    public static function getSubscribedEvents() {
        return [
            'onPageContentProcessed' => ['onPageContentProcessed', -1]
        ];
    }
    public function onPageContentProcessed(Event $event)
    {
        $page = $event['page'];
        $pluginsobject = (array) $this->config->get('plugins');
        $pageobject = $this->grav['page'];
		if (isset($pluginsobject['imgsrcset']) && !$this->isAdmin()) {
            if ($pluginsobject['imgsrcset']['enabled']) {
				$dom = new Dom;
				$dom->load($page->content());
				$images = $dom->find('img');
				$widths = $pluginsobject['imgsrcset']['widths'];
				foreach($images as $image) {
					$file = pathinfo($image->getAttribute('src'));
					$srcsets = '';
					foreach($widths as $width) {
						$srcsets .= '';
						$srcsets .= $file['dirname'].'/'.$file['filename'].'-'.$width.'.'.$file['extension'].' '.$width.'w, ';
					}
					$srcsets = rtrim($srcsets, ", ");
					$image->setAttribute('srcset', $srcsets);
					$image->setAttribute('sizes', $pluginsobject['imgsrcset']['sizes']);
				}
				$page->setRawContent($dom);
            }
        }
    }
}