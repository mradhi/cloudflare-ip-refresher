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

namespace App\Event;

use App\Entity\IPHistory;
use Symfony\Contracts\EventDispatcher\Event;

class IPChangedEvent extends Event
{
    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var IPHistory
     */
    protected $ipHistory;

    public function __construct(string $ipAddress, IPHistory $ipHistory)
    {
        $this->ipAddress = $ipAddress;
        $this->ipHistory = $ipHistory;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @return IPHistory
     */
    public function getIpHistory(): IPHistory
    {
        return $this->ipHistory;
    }
}
