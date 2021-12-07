<?php declare(strict_types=1);

namespace Lwjh\MatomoTrackingPlugin\Subscriber;

use MatomoTracker;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StoreFrontSubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            StorefrontRenderEvent::class => 'onStorefrontRender',
        ];
    }

    public function debug_to_console($data, $context = 'Debug in Console') {

        // Buffering to solve problems frameworks, like header() in this and not a solid return.
        ob_start();

        $output  = 'console.info(\'' . $context . ':\');';
        $output .= 'console.log(' . json_encode($data) . ');';
        $output  = sprintf('<script>%s</script>', $output);

        echo $output;
    }

    public function getKey(String $key) {
        return $this->systemConfigService->get("LwjhMatomoTrackingPlugin.config.$key");
    }

    /**
     * @param StorefrontRenderEvent $event
     */



    public function onStorefrontRender(StorefrontRenderEvent $event)
    {
        $matomoTracker = new MatomoTracker($this->getKey("matomoSiteId"), $this->getKey('matomoURL')."/");
        $matomoTracker->disableCookieSupport();
        $matomoTracker->doTrackPageView("Page Visit");
    }
}