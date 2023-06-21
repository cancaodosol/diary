<?php

namespace App\Entity;

use App\Repository\NoteTagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteTagsRepository::class)
 */
class NoteTags
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
     * @ORM\ManyToMany(targetEntity=UnitaryNote::class, mappedBy="tags")
     */
    private $unitaryNotes;

    public function __construct()
    {
        $this->unitaryNotes = new ArrayCollection();
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
     * @return Collection<int, UnitaryNote>
     */
    public function getUnitaryNotes(): Collection
    {
        return $this->unitaryNotes;
    }

    public function addUnitaryNote(UnitaryNote $unitaryNote): self
    {
        if (!$this->unitaryNotes->contains($unitaryNote)) {
            $this->unitaryNotes[] = $unitaryNote;
            $unitaryNote->addTag($this);
        }

        return $this;
    }

    public function removeUnitaryNote(UnitaryNote $unitaryNote): self
    {
        if ($this->unitaryNotes->removeElement($unitaryNote)) {
            $unitaryNote->removeTag($this);
        }

        return $this;
    }
}
