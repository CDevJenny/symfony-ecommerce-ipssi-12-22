<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $excerpt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?int $sold = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $status = 0;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?User $seller = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorites')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: CartsProducts::class)]
    private Collection $cartsProducts;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Brand $brand = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->users = new ArrayCollection();
        $this->cartsProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSold(): ?int
    {
        return $this->sold;
    }

    public function setSold(?int $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function setSeller(?User $seller): self
    {
        $this->seller = $seller;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
     * @return Collection<int, CartsProducts>
     */
    public function getCartsProducts(): Collection
    {
        return $this->cartsProducts;
    }

    public function addCartsProduct(CartsProducts $cartsProduct): self
    {
        if (!$this->cartsProducts->contains($cartsProduct)) {
            $this->cartsProducts->add($cartsProduct);
            $cartsProduct->setProduct($this);
        }

        return $this;
    }

    public function removeCartsProduct(CartsProducts $cartsProduct): self
    {
        if ($this->cartsProducts->removeElement($cartsProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartsProduct->getProduct() === $this) {
                $cartsProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }
}
