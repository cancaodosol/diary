<?php

namespace App\Entity;

use App\Repository\DiaryCompactRepository;
use Doctrine\ORM\Mapping as ORM;
use App\ValueObject\JapaneseDate;

/**
 * @ORM\Entity(repositoryClass=DiaryCompactRepository::class)
 */
class DiaryCompact
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
    private $diaryId;

    /**
     * @ORM\Column(name="`date`", type="date")
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

    public function __construct()
    {
        $this->setModifiedOn();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiaryId(): ?int
    {
        return $this->diaryId;
    }

    public function setDiaryId(int $diaryId): self
    {
        $this->diaryId = $diaryId;

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
        return nl2br($this->text);
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

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
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
