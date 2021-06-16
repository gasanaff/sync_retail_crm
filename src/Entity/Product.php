<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Site $site;

    /**
     * @ORM\Column(name="min_price", type="decimal", precision=10, scale=2)
     */
    private string $minPrice;

    /**
     * @ORM\Column(name="max_price", type="decimal", precision=10, scale=2)
     */
    private string $maxPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $article;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    private ?string $url;

    /**
     * @ORM\Column(name="image_url", type="string", length=510, nullable=true)
     */
    private ?string $imageUrl;

    /**
     * @ORM\Column(type="string", length=510)
     */
    private string $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $popular;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $novelty;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $recommended;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $stock;

    /**
     * @ORM\Column(type="json",options={"jsonb"=true})
     */
    private array $groups = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $manufacturer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $active;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $quantity;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $markable;

    /**
     * @ORM\OneToMany(targetEntity=Offer::class, mappedBy="product", orphanRemoval=true)
     */
    private Collection $offers;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTime $deletedAt;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getMinPrice(): ?float
    {
        return round($this->minPrice, 2);
    }

    public function setMinPrice(string $minPrice): self
    {
        $this->minPrice = number_format((float) $minPrice, 2, '.', '');

        return $this;
    }

    public function getMaxPrice(): ?float
    {
        return round($this->maxPrice, 2);
    }

    public function setMaxPrice(string $maxPrice): self
    {
        $this->maxPrice = number_format((float) $maxPrice, 2, '.', '');

        return $this;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(?string $article): self
    {
        $this->article = $article;

        return $this;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPopular(): ?bool
    {
        return $this->popular;
    }

    public function setPopular(?bool $popular): self
    {
        $this->popular = $popular;

        return $this;
    }

    public function getNovelty(): ?bool
    {
        return $this->novelty;
    }

    public function setNovelty(?bool $novelty): self
    {
        $this->novelty = $novelty;

        return $this;
    }

    public function getRecommended(): ?bool
    {
        return $this->recommended;
    }

    public function setRecommended(?bool $recommended): self
    {
        $this->recommended = $recommended;

        return $this;
    }

    public function getStock(): ?bool
    {
        return $this->stock;
    }

    public function setStock(bool $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

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

    public function getMarkable(): ?bool
    {
        return $this->markable;
    }

    public function setMarkable(?bool $markable): self
    {
        $this->markable = $markable;

        return $this;
    }

    /**
     * @return Collection|Offer[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setProduct($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getProduct() === $this) {
                $offer->setProduct(null);
            }
        }

        return $this;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
