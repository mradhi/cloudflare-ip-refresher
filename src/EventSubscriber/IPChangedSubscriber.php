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
use App\Entity\IPHistory;
use App\Event\IPChangedEvent;
use App\Event\IPHistorySynchronizeEvent;
use App\Repository\IPHistoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class IPChangedSubscriber implements EventSubscriberInterface
{
    /**
     * @var CloudflareSynchronizer
     */
    protected $cloudflareSynchronizer;

    /**
     * @var IPHistoryRepository
     */
    protected $ipHistoryRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(IPHistoryRepository $ipHistoryRepository, EventDispatcherInterface $eventDispatcher, CloudflareSynchronizer $cloudflareSynchronizer)
    {
        $this->ipHistoryRepository = $ipHistoryRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->cloudflareSynchronizer = $cloudflareSynchronizer;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            IPChangedEvent::class => 'onIPChanged'
        ];
    }

    public function onIPChanged(IPChangedEvent $event): void
    {
        // Save the new IP address
        $this->ipHistoryRepository->add(
            $history = new IPHistory($event->getIpAddress(), false)
        );

        $this->eventDispatcher->dispatch(new IPHistorySynchronizeEvent($history));
    }
}
