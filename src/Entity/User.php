<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
class User implements  UserInterface,PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addedBy = null;

    /**
     * @var Collection<int, Coffre>
     */
    #[ORM\ManyToMany(targetEntity: Coffre::class, mappedBy: 'user')]
    private Collection $coffres;

    /**
     * @var Collection<int, Historique>
     */
    #[ORM\OneToMany(targetEntity: Historique::class, mappedBy: 'changedBy')]
    private Collection $historiques;

    public function __construct()
    {
        $this->coffres = new ArrayCollection();
        $this->historiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAddedBy(): ?string
    {
        return $this->addedBy;
    }

    public function setAddedBy(?string $addedBy): static
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    /**
     * @return Collection<int, Coffre>
     */
    public function getCoffres(): Collection
    {
        return $this->coffres;
    }

    public function addCoffre(Coffre $coffre): static
    {
        if (!$this->coffres->contains($coffre)) {
            $this->coffres->add($coffre);
            $coffre->addUser($this);
        }

        return $this;
    }

    public function removeCoffre(Coffre $coffre): static
    {
        if ($this->coffres->removeElement($coffre)) {
            $coffre->removeUser($this);
        }

        return $this;
    }







    // === Méthodes of UserInterface ===

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
    public function eraseCredentials(): void
    {
        // Si tu stockes des infos sensibles temporaires
    }
}
