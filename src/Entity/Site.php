<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 * @ORM\Table(name="sites")
 */
class Site
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=510)
     */
    private string $url;

    /**
     * @ORM\Column(name="catalog_updated_at", type="datetime", nullable=true)
     */
    private ?\DateTime $catalogUpdatedAt;

    /**
     * @ORM\Column(name="catalog_loading_at", type="datetime", nullable=true)
     */
    private ?\DateTime $catalogLoadingAt;

    /**
     * @ORM\Column(name="country_iso", type="string", length=20, nullable=true)
     */
    private ?string $countryIso;

    /**
     * @ORM\OneToMany(targetEntity=ProductGroup::class, mappedBy="site", orphanRemoval=true)
     */
    private Collection $productGroups;

    /**
     * @ORM\Column(name="code", type="string", length=255)
     */
    private string $code;

    public function __construct()
    {
        $this->productGroups = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCatalogUpdatedAt(): ?\DateTimeInterface
    {
        return $this->catalogUpdatedAt;
    }

    public function setCatalogUpdatedAt(?\DateTimeInterface $catalogUpdatedAt): self
    {
        $this->catalogUpdatedAt = $catalogUpdatedAt;

        return $this;
    }

    public function getCatalogLoadingAt(): ?\DateTimeInterface
    {
        return $this->catalogLoadingAt;
    }

    public function setCatalogLoadingAt(?\DateTimeInterface $catalogLoadingAt): self
    {
        $this->catalogLoadingAt = $catalogLoadingAt;

        return $this;
    }

    public function getCountryIso(): ?string
    {
        return $this->countryIso;
    }

    public function setCountryIso(?string $countryIso): self
    {
        $this->countryIso = $countryIso;

        return $this;
    }

    /**
     * @return Collection|ProductGroup[]
     */
    public function getProductGroups(): Collection
    {
        return $this->productGroups;
    }

    public function addProductGroup(ProductGroup $productGroup): self
    {
        if (!$this->productGroups->contains($productGroup)) {
            $this->productGroups[] = $productGroup;
            $productGroup->setSite($this);
        }

        return $this;
    }

    public function removeProductGroup(ProductGroup $productGroup): self
    {
        if ($this->productGroups->removeElement($productGroup)) {
            // set the owning side to null (unless already changed)
            if ($productGroup->getSite() === $this) {
                $productGroup->setSite(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
