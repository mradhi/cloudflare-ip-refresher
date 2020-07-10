<?php
/**
 * This file is part of the Elguen, IP Refresher package.
 *
 * (c) Mohamed Radhi GUENNICHI <guennichiradhi@gmail.com> <+216 50 711 816>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\EventSubscriber;

use App\DNS\Cloudflare\CloudflareSynchronizer;
use App\Event\IPHistorySynchronizeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IPHistorySynchronizeSubscriber implements EventSubscriberInterface
{
    /**
     * @var CloudflareSynchronizer
     */
    protected $cloudflareSynchronizer;

    public function __construct(CloudflareSynchronizer $cloudflareSynchronizer)
    {
        $this->cloudflareSynchronizer = $cloudflareSynchronizer;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            IPHistorySynchronizeEvent::class => 'onSynchronize'
        ];
    }

    public function onSynchronize(IPHistorySynchronizeEvent $event): void
    {
        // Here we need to update our records on Cloudflare.
        $this->cloudflareSynchronizer->synchronize($event->getIpHistory());
    }
}
