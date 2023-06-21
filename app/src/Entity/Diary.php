<?php

namespace App\Entity;

use App\Repository\DiaryRepository;
use Doctrine\ORM\Mapping as ORM;
use App\ValueObject\HtmlText;
use App\ValueObject\JapaneseDate;

/**
 * @ORM\Entity(repositoryClass=DiaryRepository::class)
 */
class Diary
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(name="`date`", type="date", length=255)
     */
    private $date;

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

    private $title;

    private $startedAt;

    private $finishedAt;

    public function __construct()
    {
        $this->setUserId(1);
        $this->setDiv('diary');
        $this->setDate(new \DateTime('now'));
        $this->setStartedAt();
        $this->setModifiedOn();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $UserId): self
    {
        $this->userId = $UserId;

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

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStartedAt(): ?string
    {
        return $this->startedAt;
    }

    public function setStartedAt($startedAt = null): void
    {
        $this->startedAt = $startedAt == null ? (new \DateTime('now'))->format('H:i') : $startedAt;
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
        if(!$this->finishedAt) return $this->startedAt." - ".date('H:i');
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
}
