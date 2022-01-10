<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]

    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'invitations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user_exp;

    #[ORM\ManyToOne(targetEntity: user::class, inversedBy: 'invitations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user_dest;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private $status;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $deletedAt;

    public function __construct()
    {
        $this->status = false;
        $this->createdAt = new \DateTimeImmutable();
        $this->deletedAt = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserExp(): ?User
    {
        return $this->user_exp;
    }

    public function setUserExp(?User $user_exp): self
    {
        $this->user_exp = $user_exp;

        return $this;
    }

    public function getUserDest(): ?user
    {
        return $this->user_dest;
    }

    public function setUserDest(?user $user_dest): self
    {
        $this->user_dest = $user_dest;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
