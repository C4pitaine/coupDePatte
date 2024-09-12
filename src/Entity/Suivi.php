<?php

namespace App\Entity;

use App\Repository\SuiviRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SuiviRepository::class)]
class Suivi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif','image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, png, gif, webP")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:10,max:1000,minMessage:"La description doit dépasser 10 caractères",maxMessage:"La description ne doit pas dépasser 1000 caractères")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2,max:255,minMessage:"Le nom de l'animal doit dépasser 2 caractères",maxMessage:"Le nom de l'animal ne doit pas dépasser 255 caractères")]
    private ?string $animal = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAnimal(): ?string
    {
        return $this->animal;
    }

    public function setAnimal(?string $animal): static
    {
        $this->animal = $animal;

        return $this;
    }
}
