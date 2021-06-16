<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductGroupRepository::class)
 * @ORM\Table(name="product_groups")
 */
class ProductGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="productGroups")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Site $site;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(name="external_id", type="string", length=255, nullable=true)
     */
    private ?string $externalId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $active;

    /**
     * @ORM\ManyToOne(targetEntity=ProductGroup::class, inversedBy="children")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private ?ProductGroup $parent;

    /**
     * @ORM\OneToMany(targetEntity=ProductGroup::class, mappedBy="parent")
     */
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
