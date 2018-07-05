<?php

namespace Itgro\Social;

use Itgro\Social\Handler\Base;
use Itgro\Social\Handler\Facebook;
use Itgro\Social\Handler\GooglePlus;
use Itgro\Social\Handler\MailRu;
use Itgro\Social\Handler\Odnoklassniki;
use Itgro\Social\Handler\Pinterest;
use Itgro\Social\Handler\Twitter;
use Itgro\Social\Handler\VKontakte;

class Sharing
{
    const EXPAND_HANDLERS_EVENT = 'onCreateSharingHandlersList';

    protected $handlers = [
        'vk' => VKontakte::class,
        'fb' => Facebook::class,
        'twitter' => Twitter::class,
        'ok' => Odnoklassniki::class,
        'mail_ru' => MailRu::class,
        'pin' => Pinterest::class,
        'g_plus' => GooglePlus::class,
    ];

    private $title;
    private $description = '';
    private $url = null;
    private $image = null;

    public function __construct($title, $description = '', $url = null, $image = null)
    {
        $this->title = $title;
        $this->description = $description;

        $this->setUrl($url);
        $this->setImage($image);
    }

    public function handlers($codes = [])
    {
        $result = [];
        foreach ($this->getHandlers() as $code => $handler) {
            if (!empty($codes) && !in_array($code, $codes)) {
                continue;
            }

            /** @var Base $entity */
            $result[$code] = new $handler([
                'title' => $this->title,
                'description' => $this->description,
                'url' => $this->url,
                'image' => $this->image,
            ]);
        }

        return $result;
    }

    private function setUrl($url = null)
    {
        if (!$url) {
            $url = application()->GetCurUri();
        }

        $this->url = $this->formatUrl($url);
    }

    private function setImage($image)
    {
        if (is_numeric($image) && $image > 0) {
            $image = get_file($image);
        } elseif (is_array($image) && !empty($image)) {
            $image['src'] = array_get($image, 'SRC');
        }

        $this->image = (!empty($image) && strlen(array_get($image, 'src')) > 0) ?
            $this->formatUrl(array_get($image, 'src')) :
            null;
    }

    private function formatUrl($url)
    {
        if (stripos($url, '://') !== false) {
            return $url;
        }

        return sprintf(
            '%s://%s%s',
            (request()->isHttps()) ? 'https' : 'http',
            request()->getHttpHost(),
            $url
        );
    }

    private function getHandlers()
    {
        return array_merge(
            $this->handlers,
            collect_event_handlers('extensions', self::EXPAND_HANDLERS_EVENT)
        );
    }
}
