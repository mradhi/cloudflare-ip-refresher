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

namespace App\Service;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use GuzzleHttp\Exception\RequestException;
use stdClass;

class CloudflareService
{
    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var DNS
     */
    protected $dns;

    /**
     * @var Zones
     */
    protected $zones;

    /**
     * @var stdClass
     */
    protected $userDetails;

    public function __construct(string $email, string $apiKey)
    {
        $this->adapter = new Guzzle(
            new APIKey($email, $apiKey)
        );
    }

    public function getZoneID(string $domainName): string
    {
        return $this->getZones()->getZoneID($domainName);
    }

    public function getDNS(): DNS
    {
        if (null === $this->dns) {
            $this->dns = new DNS($this->adapter);
        }

        return $this->dns;
    }


    public function isConnected(): bool
    {
        try {
            $this->getUserDetails();
        } catch (RequestException $exception) {
            return false;
        }

        return true;
    }

    public function getUserDetails(): stdClass
    {
        if (null === $this->userDetails) {
            $this->userDetails = $this->getUser()
                ->getUserDetails();
        }

        return $this->userDetails;
    }

    protected function getZones(): Zones
    {
        if (null === $this->zones) {
            $this->zones = new Zones($this->adapter);
        }

        return $this->zones;
    }

    protected function getUser(): User
    {
        if (null === $this->user) {
            $this->user = new User($this->adapter);
        }

        return $this->user;
    }
}
