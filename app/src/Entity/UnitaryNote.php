<?php

namespace App\Entity;

use App\Repository\UnitaryNoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\ValueObject\HtmlText;
use App\ValueObject\JapaneseDate;

/**
 * @ORM\Entity(repositoryClass=UnitaryNoteRepository::class)
 */
class UnitaryNote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(name="`div`", type="string", length=255)
     */
    private $div;

    /**
     * @ORM\Column(name="`date`", type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="`text`", type="text", length=65532)
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedOn;

    /**
     * @ORM\ManyToMany(targetEntity=NoteTags::class, inversedBy="unitaryNotes")
     */
    private $tags;

    public function __construct()
    {
        $this->setUserId(1);
        $this->setDiv('');
        $this->setDate(new \DateTime('now'));
        $this->setModifiedOn();
        $this->tags = new ArrayCollection();
    }

    public function clearItem(): void
    {
        $this->id = null;
        $this->setDiv('');
        $this->setTitle('');
        $this->setText('');
        $this->createdOn = null;
        $this->modifiedOn = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDiv(): ?string
    {
        return $this->div;
    }

    public function setDiv(string $div): self
    {
        $this->div = $div;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getDateString(): ?string
    {
        return $this->date->format('Y-n-j');
    }

    public function getDateYoubi(): ?string
    {
        $jd = new JapaneseDate($this->date);
        return $jd->getYoubi();
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function getModifiedOn(): ?\DateTimeInterface
    {
        return $this->modifiedOn;
    }

    /**
     * @ORM\PrePersist
     **/
    public function setModifiedOn(): void
    {
        $this->modifiedOn = new \DateTime('now');
        if ($this->createdOn === null) {
            $this->createdOn = new \DateTime('now');
        }
    }

    /**
     * @return Collection<int, NoteTags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(NoteTags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(NoteTags $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
