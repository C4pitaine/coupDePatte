<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $genre = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    private ?string $race = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $adoptionDate = null;

    #[ORM\Column]
    private ?bool $adopted = null;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'animalId', orphanRemoval: true)]
    private Collection $suivis;

    /**
     * @var Collection<int, Friandise>
     */
    #[ORM\ManyToMany(targetEntity: Friandise::class, inversedBy: 'animals')]
    private Collection $friandise;

    public function __construct()
    {
        $this->suivis = new ArrayCollection();
        $this->friandise = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAdoptionDate(): ?\DateTimeInterface
    {
        return $this->adoptionDate;
    }

    public function setAdoptionDate(\DateTimeInterface $adoptionDate): static
    {
        $this->adoptionDate = $adoptionDate;

        return $this;
    }

    public function isAdopted(): ?bool
    {
        return $this->adopted;
    }

    public function setAdopted(bool $adopted): static
    {
        $this->adopted = $adopted;

        return $this;
    }

    /**
     * @return Collection<int, Suivi>
     */
    public function getSuivis(): Collection
    {
        return $this->suivis;
    }

    public function addSuivi(Suivi $suivi): static
    {
        if (!$this->suivis->contains($suivi)) {
            $this->suivis->add($suivi);
            $suivi->setAnimalId($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getAnimalId() === $this) {
                $suivi->setAnimalId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friandise>
     */
    public function getFriandise(): Collection
    {
        return $this->friandise;
    }

    public function addFriandise(Friandise $friandise): static
    {
        if (!$this->friandise->contains($friandise)) {
            $this->friandise->add($friandise);
        }

        return $this;
    }

    public function removeFriandise(Friandise $friandise): static
    {
        $this->friandise->removeElement($friandise);

        return $this;
    }
}
