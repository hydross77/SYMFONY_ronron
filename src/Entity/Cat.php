<?php

namespace App\Entity;

use App\Repository\CatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatRepository::class)]
class Cat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $age = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $breed = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $tattoo = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $sterelized = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $designCoat = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $lengthCoat = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $sexe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chip = null;

    #[ORM\ManyToOne(inversedBy: 'cat')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorite')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'cat', targetEntity: Announce::class)]
    private Collection $announces;

    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'cats')]
    private Collection $color;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->announces = new ArrayCollection();
        $this->color = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    public function getTattoo(): ?string
    {
        return $this->tattoo;
    }

    public function setTattoo(?string $tattoo): self
    {
        $this->tattoo = $tattoo;

        return $this;
    }

    public function getSterelized(): ?string
    {
        return $this->sterelized;
    }

    public function setSterelized(?string $sterelized): self
    {
        $this->sterelized = $sterelized;

        return $this;
    }

    public function getDesignCoat(): ?string
    {
        return $this->designCoat;
    }

    public function setDesignCoat(string $designCoat): self
    {
        $this->designCoat = $designCoat;

        return $this;
    }

    public function getLengthCoat(): ?string
    {
        return $this->lengthCoat;
    }

    public function setLengthCoat(?string $lengthCoat): self
    {
        $this->lengthCoat = $lengthCoat;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getChip(): ?string
    {
        return $this->chip;
    }

    public function setChip(?string $chip): self
    {
        $this->chip = $chip;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addFavorite($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFavorite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Announce>
     */
    public function getAnnounces(): Collection
    {
        return $this->announces;
    }

    public function addAnnounce(Announce $announce): self
    {
        if (!$this->announces->contains($announce)) {
            $this->announces->add($announce);
            $announce->setCat($this);
        }

        return $this;
    }

    public function removeAnnounce(Announce $announce): self
    {
        if ($this->announces->removeElement($announce)) {
            // set the owning side to null (unless already changed)
            if ($announce->getCat() === $this) {
                $announce->setCat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Color>
     */
    public function getColor(): Collection
    {
        return $this->color;
    }

    public function addColor(Color $color): self
    {
        if (!$this->color->contains($color)) {
            $this->color->add($color);
        }

        return $this;
    }

    public function removeColor(Color $color): self
    {
        $this->color->removeElement($color);

        return $this;
    }
}
