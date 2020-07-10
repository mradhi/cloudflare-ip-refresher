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

class InternetChecker
{
    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        // Open socket connection
        $connected = @fsockopen("www.google.com", 80, $errno, $errstr, 3);

        // Clean memory
        unset($errno, $errstr);

        if (false !== $connected){
            // Close socket connection
            fclose($connected);

            return true;
        }

        return false;
    }
}
