<?php

namespace App\Entity;

use App\Repository\ParrainageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParrainageRepository::class)]
class Parrainage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(min:10,max:1000,notInRangeMessage:"Le montant du parrainage doit être supérieur à 10€ et ne peut pas dépasser 1000€")]
    private ?int $montant = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'parrainages')]
    private Collection $user;

    /**
     * @var Collection<int, Animal>
     */
    #[ORM\ManyToMany(targetEntity: Animal::class, inversedBy: 'parrainages')]
    private Collection $animal;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->animal = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimal(): Collection
    {
        return $this->animal;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animal->contains($animal)) {
            $this->animal->add($animal);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        $this->animal->removeElement($animal);

        return $this;
    }
}
