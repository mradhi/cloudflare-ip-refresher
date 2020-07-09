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

use Symfony\Contracts\EventDispatcher\Event;

class InternetCheckEvent extends Event
{
    /**
     * @var bool
     */
    protected $connected;

    public function __construct(bool $connected)
    {
        $this->connected = $connected;
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }
}
