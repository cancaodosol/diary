<?php

namespace App\Entity;

use App\Repository\HouseholdAccountRecordRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HouseholdAccountRecordRepository::class)
 */
class HouseholdAccountRecord
{
    public const TYPE_EXPENSE = '支出';
    public const TYPE_INCOME = '収入';
    public const TYPES = [self::TYPE_EXPENSE, self::TYPE_INCOME];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UnitaryNote::class, inversedBy="householdAccountRecords")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unitaryNote;

    /**
     * @ORM\ManyToOne(targetEntity=JournalCategory::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $journalCategory;

    /**
     * @ORM\Column(name="`date`", type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $itemName;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=16, options={"default"="支出"})
     */
    private $type = self::TYPE_EXPENSE;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnitaryNote(): ?UnitaryNote
    {
        return $this->unitaryNote;
    }

    public function setUnitaryNote(UnitaryNote $unitaryNote): self
    {
        $this->unitaryNote = $unitaryNote;

        return $this;
    }

    public function getJournalCategory(): ?JournalCategory
    {
        return $this->journalCategory;
    }

    public function setJournalCategory(JournalCategory $journalCategory): self
    {
        $this->journalCategory = $journalCategory;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    public function setItemName(string $itemName): self
    {
        $this->itemName = $itemName;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, self::TYPES, true)) {
            throw new \InvalidArgumentException(sprintf('区分は %s のいずれかを指定してください。', implode('/', self::TYPES)));
        }

        $this->type = $type;

        return $this;
    }
}
