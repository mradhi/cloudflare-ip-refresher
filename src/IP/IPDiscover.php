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

namespace App\IP;

class IPDiscover
{
    /**
     * @var string
     */
    protected $lookupURL;

    public function __construct(string $lookupURL)
    {
        $this->lookupURL = $lookupURL;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return file_get_contents($this->lookupURL);
    }
}
