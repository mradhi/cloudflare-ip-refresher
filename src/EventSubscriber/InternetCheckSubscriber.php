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

use App\Entity\InternetStatus;
use App\Event\InternetCheckEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InternetCheckSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            InternetCheckEvent::class => 'onCheck'
        ];
    }

    public function onCheck(InternetCheckEvent $event): void
    {
        // Create a new InternetStatus entry in the DB.
        $status = new InternetStatus(
            $event->isConnected()
        );

        $this->entityManager->persist($status);
        $this->entityManager->flush();
    }
}
