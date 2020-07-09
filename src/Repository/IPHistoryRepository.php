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

namespace App\Repository;

use App\Entity\IPHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class IPHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IPHistory::class);
    }

    public function findLatestByIpAddress(string $ipAddress): ?IPHistory
    {
        return $this->findOneBy(['ipAddress' => $ipAddress], [
            'createdAt' => Criteria::DESC
        ]);
    }

    public function add(IPHistory $history): void
    {
        $this->_em->persist($history);
        $this->_em->flush();
    }
}
