<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;

trait Timestampable
{
    #[Column(type: 'datetime', nullable: false)]
    private ?DateTime $createdAt;

    #[Column(type: 'datetime', nullable: false)]
    private ?DateTime $updatedAt;

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    #[PrePersist]
    public function beforePersist()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function beforeUpdate()
    {
        $this->updatedAt = new DateTime();
    }
}