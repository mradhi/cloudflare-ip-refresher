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

class IPHistorySynchronizeEvent
{
    /**
     * @var IPHistory
     */
    protected $ipHistory;

    public function __construct(IPHistory $ipHistory)
    {
        $this->ipHistory = $ipHistory;
    }

    /**
     * @return IPHistory
     */
    public function getIpHistory(): IPHistory
    {
        return $this->ipHistory;
    }
}
