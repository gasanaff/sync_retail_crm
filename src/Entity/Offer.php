<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 * @ORM\Table(name="offers")
 */
class Offer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="offers")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Product $product;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Site $site;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private ?string $price;

    /**
     * @ORM\Column(type="json",options={"jsonb"=true})
     */
    private ?array $images = [];

    /**
     * @ORM\Column(name="external_id", type="string", length=255, nullable=true)
     */
    private ?string $externalId;

    /**
     * @ORM\Column(name="xml_id", type="string", length=255, nullable=true)
     */
    private ?string $xmlId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $article;

    /**
     * @ORM\Column(type="json", options={"jsonb"=true})
     */
    private ?array $prices = [];

    /**
     * @ORM\Column(name="purchase_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private ?string $purchasePrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $vatRate;

    /**
     * @ORM\Column(type="json", nullable=true, options={"jsonb"=true})
     */
    private ?array $properties = [];

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private ?string $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private ?string $weight;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private ?string $length;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private ?string $width;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=true)
     */
    private ?string $height;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $active;

    /**
     * @ORM\Column(type="json",nullable=true, options={"jsonb"=true})
     */
    private ?array $unit = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $barcode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

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

    public function getPrice(): ?float
    {
        return round($this->price, 2);
    }

    public function setPrice(?string $price): self
    {
        if (null === $price) {
            $this->price = null;
        } else {
            $this->price = number_format((float) $price, 2, '.', '');
        }

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getXmlId(): ?string
    {
        return $this->xmlId;
    }

    public function setXmlId(?string $xmlId): self
    {
        $this->xmlId = $xmlId;

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

    public function getPrices(): ?array
    {
        return $this->prices;
    }

    public function setPrices(array $prices): self
    {
        $this->prices = $prices;

        return $this;
    }

    public function getPurchasePrice(): ?string
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(?string $purchasePrice): self
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getVatRate(): ?string
    {
        return $this->vatRate;
    }

    public function setVatRate(?string $vatRate): self
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function setProperties(?array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getWeight(): ?float
    {
        return round($this->weight, 3);
    }

    public function setWeight(?string $weight): self
    {
        if (null === $weight) {
            $this->width = null;
        } else {
            $this->weight = number_format((float) $weight, 3, '.', '');
        }

        return $this;
    }

    public function getLength(): ?float
    {
        return round($this->length, 3);
    }

    public function setLength(?string $length): self
    {
        if (null === $length) {
            $this->length = null;
        } else {
            $this->length = number_format((float) $length, 3, '.', '');
        }

        return $this;
    }

    public function getWidth(): ?float
    {
        return round($this->width, 3);
    }

    public function setWidth(?string $width): self
    {
        if (null === $width) {
            $this->width = null;
        } else {
            $this->width = number_format((float) $width, 3, '.', '');
        }

        return $this;
    }

    public function getHeight(): ?float
    {
        return round($this->height, 3);
    }

    public function setHeight(?string $height): self
    {
        if (null === $height) {
            $this->height = null;
        } else {
            $this->height = number_format((float) $height, 3, '.', '');
        }

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

    public function getUnit(): ?array
    {
        return $this->unit;
    }

    public function setUnit(?array $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this;
    }
}
