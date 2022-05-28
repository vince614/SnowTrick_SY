<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Figure::class, mappedBy="group")
     */
    private $figure;

    public function __construct()
    {
        $this->figure = new ArrayCollection();
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

    /**
     * @return Collection|Figure[]
     */
    public function getFigure(): Collection
    {
        return $this->figure;
    }

    public function addFigure(Figure $figure): self
    {
        if (!$this->figure->contains($figure)) {
            $this->figure[] = $figure;
            $figure->setGroupFigure($this);
        }

        return $this;
    }

    public function removeFigure(Figure $figure): self
    {
        if ($this->figure->removeElement($figure)) {
            // set the owning side to null (unless already changed)
            if ($figure->getGroupFigure() === $this) {
                $figure->setGroupFigure(null);
            }
        }

        return $this;
    }
}
