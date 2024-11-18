<?php

namespace App\Entity;

use App\Repository\NoteTagsRepository;
use App\ValueObject\HtmlText;
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $parentTagId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=UnitaryNote::class, mappedBy="tags")
     */
    private $unitaryNotes;

    /**
     * @ORM\Column(name="`description`", type="text", length=65532, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="`text`", type="text", length=65532, nullable=true)
     */
    private $text;

    private $childrenTags;

    public function __construct()
    {
        $this->unitaryNotes = new ArrayCollection();
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->getName(),
            "text" => $this->getText(),
            "textHtml" => $this->getTextHtml(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentTagId(): ?int
    {
        return $this->parentTagId;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getTextHtml(): ?string
    {
        return (new HtmlText($this->text))->getTextHtml();
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

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

    public function appendChildrenTag($tag): self
    {
        if(!$this->childrenTags) $this->childrenTags = [];
        $this->childrenTags[] = $tag;
        return $this;
    }

    public function getChildrenTags(): Collection
    {
        return $this->childrenTags;
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
