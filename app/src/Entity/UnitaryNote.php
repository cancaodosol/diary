<?php

namespace App\Entity;

use DateTime;
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
     * @ORM\Column(name="`text`", type="text", length=65532, nullable=true)
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

    private $keyword;

    private $startedAt;

    private $finishedAt;

    public function __construct()
    {
        $this->setUserId(1);
        $this->setDiv('');
        $this->setDateAndStartedAt(new \DateTime('now'));
        $this->setText('');
        $this->setModifiedOn();
        $this->tags = new ArrayCollection();
    }

    public function toArray()
    {
        $tags = [];
        foreach ($this->getTags() as $tag) {
            $tags[] = $tag->toArray();
        }
        return [
            "id" => $this->id,
            "userId" => $this->userId,
            "date" => $this->date,
            "dateString" => $this->getDateString(),
            "dateStringWithYoubi" => $this->getDateStringWithYoubi(),
            "preDateString" => $this->getPreDateString(),
            "nextDateString" => $this->getNextDateString(),
            "title" => $this->title,
            "text" => $this->text,
            "textHtml" => $this->getTextHtml(),
            "tags" => $tags,
        ];
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

    public function getDateStringWithYoubi(): ?string
    {
        $date = $this->getDateString();
        $yobi = $this->getDateYoubi();
        return $date .' ('. $yobi.')';
    }

    public function getPreDateString(): ?string
    {
        $nowDate = $this->getDateString();
        return date('Y-n-j', strtotime($nowDate.' -1 days'));
    }

    public function getNextDateString(): ?string
    {
        $nowDate = $this->getDateString();
        return date('Y-n-j', strtotime($nowDate.' +1 days'));
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

    public function getTitleWithoutDate(): ?string
    {
        return substr($this->title, 16);
    }

    public function getTitleDate(): ?string
    {
        return substr($this->title, 0, 13);
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
        $textHtml = $this->keyword ? str_replace($this->keyword, '<span style="background-color: yellow;">'.$this->keyword.'</span>', $this->text) : $this->text;
        return (new HtmlText($textHtml))->getTextHtml();
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function setKeyword(?string $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function getStartedAt(): ?string
    {
        return $this->startedAt;
    }

    public function setStartedAt($startedAt = null): void
    {
        $this->startedAt = $startedAt == null ? (new \DateTime('now'))->format('H:i') : $startedAt;
    }

    public function setDateAndStartedAt(\DateTimeInterface $date): void
    {
        $hours = (int)$date->format('H');
        $day = $date->format('Y-m-d');
        if($hours < 5)
        {
            $day = date('Y-m-d', strtotime($day.' -1 days'));
        }
        $this->setDate(new DateTime($day));
        $this->setStartedAt($this->createTimeAt($date));
    }

    public function createTimeAt(\DateTimeInterface $date): string
    {
        $hours = (int)$date->format('H');
        $minutes = (int)$date->format('i');
        if($hours < 5)
        {
            $hours += 24;
        }
        return sprintf("%'.02d:%'.02d", $hours, $minutes);
    }

    public function getFinishedAt(): ?string
    {
        return $this->finishedAt;
    }

    public function setFinishedAt($finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    public function getStartedAndFinishedAt(): string
    {
        if(!$this->finishedAt)
        {
            return $this->startedAt." - ".$this->createTimeAt(new \DateTime('now'));
        }
        return $this->startedAt." - ".$this->finishedAt;
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
