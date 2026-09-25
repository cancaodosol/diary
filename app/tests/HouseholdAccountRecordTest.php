<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use App\Entity\HouseholdAccountRecord;
use App\Entity\JournalCategory;
use App\Entity\UnitaryNote;

class HouseholdAccountRecordTest extends TestCase
{
    public function testItemNameGetterSetter(): void
    {
        // Given: 新規 HouseholdAccountRecord
        $record = new HouseholdAccountRecord();

        // When: 品名をセット
        $record->setItemName('コーヒー');

        // Then: 同じ値が返る
        $this->assertSame('コーヒー', $record->getItemName());
    }

    public function testAmountGetterSetter(): void
    {
        // Given: 新規 HouseholdAccountRecord
        $record = new HouseholdAccountRecord();

        // When: 金額をセット
        $record->setAmount(500);

        // Then: 同じ値が返る
        $this->assertSame(500, $record->getAmount());
    }

    public function testDateGetterSetter(): void
    {
        // Given: 新規 HouseholdAccountRecord
        $record = new HouseholdAccountRecord();
        $date = new \DateTime('2026-09-24');

        // When: 日付をセット
        $record->setDate($date);

        // Then: 同じ値が返る
        $this->assertSame($date, $record->getDate());
    }

    public function testJournalCategoryGetterSetter(): void
    {
        // Given: 新規 HouseholdAccountRecord と JournalCategory
        $record = new HouseholdAccountRecord();
        $category = new JournalCategory();
        $category->setName('食費');

        // When: 仕訳分類をセット
        $record->setJournalCategory($category);

        // Then: 同じインスタンスが返る
        $this->assertSame($category, $record->getJournalCategory());
    }

    public function testUnitaryNoteGetterSetter(): void
    {
        // Given: 新規 HouseholdAccountRecord と UnitaryNote
        $record = new HouseholdAccountRecord();
        $note = new UnitaryNote();

        // When: UnitaryNote をセット
        $record->setUnitaryNote($note);

        // Then: 同じインスタンスが返る
        $this->assertSame($note, $record->getUnitaryNote());
    }

    public function testIdIsNullBeforePersisting(): void
    {
        // Given: 新規 HouseholdAccountRecord（永続化前）
        $record = new HouseholdAccountRecord();

        // Then: IDはまだ採番されていない
        $this->assertNull($record->getId());
    }

    public function testTypeDefaultsToExpenseOnNewRecord(): void
    {
        // Given: 新規 HouseholdAccountRecord

        // When: 区分をセットせずに取得する
        $record = new HouseholdAccountRecord();

        // Then: 初期値は「支出」
        $this->assertSame(HouseholdAccountRecord::TYPE_EXPENSE, $record->getType());
    }

    public function testCanSetTypeToExpense(): void
    {
        // Given: 新規 HouseholdAccountRecord
        $record = new HouseholdAccountRecord();

        // When: 区分に「支出」をセット
        $record->setType(HouseholdAccountRecord::TYPE_EXPENSE);

        // Then: 「支出」が返る
        $this->assertSame(HouseholdAccountRecord::TYPE_EXPENSE, $record->getType());
    }

    public function testCanSetTypeToIncome(): void
    {
        // Given: 新規 HouseholdAccountRecord
        $record = new HouseholdAccountRecord();

        // When: 区分に「収入」をセット
        $record->setType(HouseholdAccountRecord::TYPE_INCOME);

        // Then: 「収入」が返る
        $this->assertSame(HouseholdAccountRecord::TYPE_INCOME, $record->getType());
    }

    public function testSetTypeRejectsInvalidValue(): void
    {
        // Given: 新規 HouseholdAccountRecord
        $record = new HouseholdAccountRecord();

        // Then: 「支出」「収入」以外の値は拒否される
        $this->expectException(\InvalidArgumentException::class);

        // When: 不正な区分をセット
        $record->setType('不正な区分');
    }
}
