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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class InternetStatus
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\InternetStatusRepository")
 * @ORM\Table(name="internet_status", indexes={
 *     @ORM\Index(name="internet_status_idx", columns={"connected", "created_at", "updated_at"})
 * })
 */
class InternetStatus implements ResourceInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $connected;

    public function __construct(bool $connected = null)
    {
        $this->connected = $connected;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isConnected(): ?bool
    {
        return $this->connected;
    }

    /**
     * @param bool $connected
     */
    public function setConnected(bool $connected): void
    {
        $this->connected = $connected;
    }
}
