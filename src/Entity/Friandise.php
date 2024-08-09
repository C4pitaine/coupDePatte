<?php

namespace App\Entity;

use App\Repository\FriandiseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FriandiseRepository::class)]
class Friandise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5,max:255,minMessage:"Le nom de la friandise doit dépasser 5 caractères",maxMessage:"Le nom de la friandise ne doit pas dépasser 255 caractères")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\Range(min:0,max:500,notInRangeMessage:"Le prix doit être entre 0 et 500€")]
    private ?float $price = null;

    /**
     * @var Collection<int, Animal>
     */
    #[ORM\ManyToMany(targetEntity: Animal::class, mappedBy: 'friandise')]
    private Collection $animals;

    #[ORM\Column(length: 255)]
    private ?string $animal = null;

    #[ORM\Column(length: 255)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif','image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, png, gif, webP")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $image = null;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->addFriandise($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            $animal->removeFriandise($this);
        }

        return $this;
    }

    public function getAnimal(): ?string
    {
        return $this->animal;
    }

    public function setAnimal(string $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
