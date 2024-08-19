<?php

namespace App\Entity;

use App\Repository\AdoptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdoptionRepository::class)]
class Adoption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2,max:255,minMessage:"Le nom de l'animal doit dépasser 2 caractères",maxMessage:"Le nom de l'animal ne doit pas dépasser 255 caractères")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\Range(min:0,max:40,notInRangeMessage:"L'âge doit être entre 0 et 40ans")]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif','image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, png, gif, webP")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:50,minMessage:'La description doit dépasser 50 caractères',groups:["formTwo"])]
    private ?string $description = null;

    /**
     * @var Collection<int, Indispensable>
     */
    #[ORM\ManyToMany(targetEntity: Indispensable::class, inversedBy: 'adoptions')]
    private Collection $indispensables;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2,max:255,minMessage:"La race de l'animal doit dépasser 2 caractères",maxMessage:"La race de l'animal ne doit pas dépasser 255 caractères",groups:["formTwo"])]
    private ?string $race = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message:"Vous devez renseigner une adresse email valide",groups:["formTwo"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:10,max:10,exactMessage:'Votre numéro de téléphone doit faire 10 chiffres',groups:["formTwo"])]
    private ?string $tel = null;

    public function __construct()
    {
        $this->indispensables = new ArrayCollection();
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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

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

    /**
     * @return Collection<int, Indispensable>
     */
    public function getIndispensables(): Collection
    {
        return $this->indispensables;
    }

    public function addIndispensable(Indispensable $indispensable): static
    {
        if (!$this->indispensables->contains($indispensable)) {
            $this->indispensables->add($indispensable);
        }

        return $this;
    }

    public function removeIndispensable(Indispensable $indispensable): static
    {
        $this->indispensables->removeElement($indispensable);

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }
}
