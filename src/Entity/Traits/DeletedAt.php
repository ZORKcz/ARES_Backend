<?php

namespace App\GiftServices\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DeletedAt
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $deletedAt = null;

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletionTime): static
    {
        $this->deletedAt = $deletionTime;

        return $this;
    }

    public function delete(): static
    {
        $this->deletedAt = new \DateTimeImmutable('now');

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
