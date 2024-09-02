<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
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

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $genre = null;

    #[ORM\Column]
    #[Assert\Range(min:0,max:40,notInRangeMessage:"L'âge doit être entre 0 et 40ans")]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2,max:255,minMessage:"La race de l'animal doit dépasser 2 caractères",maxMessage:"La race de l'animal ne doit pas dépasser 255 caractères")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $race = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:50,minMessage:"La description de l'animal doit dépasser 50 caractères")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $adoptionDate = null;

    #[ORM\Column]
    private ?bool $adopted = null;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'animal', orphanRemoval: true)]
    private Collection $suivis;

    /**
     * @var Collection<int, Friandise>
     */
    #[ORM\ManyToMany(targetEntity: Friandise::class, inversedBy: 'animals')]
    private Collection $friandise;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'animal', orphanRemoval: true)]
    private Collection $images;

    /**
     * @var Collection<int, Indispensable>
     */
    #[ORM\ManyToMany(targetEntity: Indispensable::class, inversedBy: 'animals')]
    private Collection $indispensables;

    #[ORM\Column(length: 255)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif','image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, png, gif, webP")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $coverImage = null;

    /**
     * @var Collection<int, Favori>
     */
    #[ORM\ManyToMany(targetEntity: Favori::class, mappedBy: 'animal')]
    private Collection $favoris;

    public function __construct()
    {
        $this->suivis = new ArrayCollection();
        $this->friandise = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->indispensables = new ArrayCollection();
        $this->favoris = new ArrayCollection();
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

    public function setAdoptionDate(?\DateTimeInterface $adoptionDate): static
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
            $suivi->setAnimal($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getAnimal() === $this) {
                $suivi->setAnimal(null);
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

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAnimal($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAnimal() === $this) {
                $image->setAnimal(null);
            }
        }

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

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * @return Collection<int, Favori>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favori $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->addAnimal($this);
        }

        return $this;
    }

    public function removeFavori(Favori $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            $favori->removeAnimal($this);
        }

        return $this;
    }
}
