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

namespace App\DNS\Cloudflare;

use App\DNS\Exception\AuthenticationException;
use App\Entity\IPHistory;
use App\Service\CloudflareService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CloudflareSynchronizer
{
    public const SLEEP_BETWEEN_OPERATIONS = 2; // per seconds
    /**
     * @var CloudflareService
     */
    protected $cloudflareService;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(CloudflareService $cloudflareService, EntityManagerInterface $entityManager)
    {
        $this->cloudflareService = $cloudflareService;
        $this->entityManager     = $entityManager;
    }

    public function synchronize(IPHistory $ipHistory, string $domainName, string $type = 'A'): void
    {
        if (!$this->cloudflareService->isConnected()) {
            throw new AuthenticationException('Could not connect to Cloudflare.');
        }

        // Get the zone ID
        $zoneID = $this->cloudflareService->getZoneID($domainName);

        // Fetch all DNS records
        $dnsService = $this->cloudflareService->getDNS();
        $dnsList    = $dnsService->listRecords($zoneID, $type, '', '', 1, 100);
        $ipAddress  = $ipHistory->getIpAddress();
        $errors     = 0;

        foreach ($dnsList->result as $record) {
            $response = $dnsService->updateRecordDetails($zoneID, $record->id, [
                'type'    => $record->type,
                'name'    => $record->name,
                'content' => $ipAddress,
                'ttl'     => $record->ttl,
                'proxied' => $record->proxied
            ]);

            if (!$response->success) {
                $errors += 1;
            }

            sleep(self::SLEEP_BETWEEN_OPERATIONS);
        }

        if ($errors === 0) {
            $ipHistory->setSynchronized(true);

            $this->entityManager->flush();
        }
    }
}
