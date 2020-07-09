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

use App\Entity\IPHistory;
use App\Event\IPChangedEvent;
use App\Event\IPDiscoverEvent;
use App\Event\IPHistoryNotSynchronizedEvent;
use App\Repository\IPHistoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class IPDiscoverSubscriber implements EventSubscriberInterface
{
    /**
     * @var IPHistoryRepository
     */
    protected $ipHistoryRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(IPHistoryRepository $ipHistoryRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->ipHistoryRepository = $ipHistoryRepository;
        $this->eventDispatcher     = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            IPDiscoverEvent::class => 'onDiscover'
        ];
    }

    public function onDiscover(IPDiscoverEvent $event): void
    {
        $ipAddress = $event->getIpAddress();
        $history   = $this->ipHistoryRepository->findLatest();

        if (null === $history || $history->getIpAddress() !== $ipAddress) {
            // Save the new IP address
            $this->ipHistoryRepository->add(
                $history = new IPHistory($ipAddress, false)
            );
            // Dispatch IP changed event
            $this->eventDispatcher->dispatch(new IPChangedEvent($ipAddress, $history));

            // Skip.
            return;
        }

        if ($history && !$history->isSynchronized()) {
            $this->eventDispatcher->dispatch(new IPHistoryNotSynchronizedEvent($history));
        }
    }
}
