<?php

namespace App\GiftServices\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAt
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $createdAt;

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable('now');
    }
}
