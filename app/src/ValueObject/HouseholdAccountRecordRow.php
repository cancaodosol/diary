<?php

namespace App\ValueObject;

/**
 * バリデーション済みの家計簿入力1行分の値を保持する。
 */
class HouseholdAccountRecordRow
{
    private int $id;

    private string $itemName;

    private int $amount;

    private int $journalCategoryId;

    private string $type;

    public function __construct(int $id, string $itemName, int $amount, int $journalCategoryId, string $type)
    {
        $this->id = $id;
        $this->itemName = $itemName;
        $this->amount = $amount;
        $this->journalCategoryId = $journalCategoryId;
        $this->type = $type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getJournalCategoryId(): int
    {
        return $this->journalCategoryId;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
