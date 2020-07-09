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
 * Class IPHistory
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\IPHistoryRepository")
 * @ORM\Table(name="ip_history", indexes={
 *     @ORM\Index(name="ip_history_idx", columns={"created_at", "updated_at", "ip_address", "synchronized"})
 * })
 */
class IPHistory implements ResourceInterface, TimestampableInterface
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $ipAddress;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $synchronized;

    public function __construct(string $ipAddress = null, bool $synchronized = false)
    {
        $this->ipAddress    = $ipAddress;
        $this->synchronized = $synchronized;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @param bool $synchronized
     */
    public function setSynchronized(bool $synchronized): void
    {
        $this->synchronized = $synchronized;
    }

    /**
     * @return bool
     */
    public function isSynchronized(): ?bool
    {
        return $this->synchronized;
    }
}
