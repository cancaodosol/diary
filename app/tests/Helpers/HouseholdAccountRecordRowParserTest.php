<?php

namespace App\Tests\Helpers;

use PHPUnit\Framework\TestCase;

use App\Entity\HouseholdAccountRecord;
use App\Helpers\HouseholdAccountRecordRowParser;
use App\ValueObject\HouseholdAccountRecordRow;

class HouseholdAccountRecordRowParserTest extends TestCase
{
    public function testReturnsNoRowsWhenAllInputsAreEmpty(): void
    {
        // Given: 全項目が空の1行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse([''], [''], [''], ['']);

        // Then: 行もエラーも発生しない
        $this->assertSame([], $result['rows']);
        $this->assertSame([], $result['errors']);
    }

    public function testReturnsNoRowsWhenNoInputsGiven(): void
    {
        // Given: 空配列（入力行が1つもない）
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse([], [], [], []);

        // Then: 行もエラーも発生しない
        $this->assertSame([], $result['rows']);
        $this->assertSame([], $result['errors']);
    }

    public function testParsesSingleValidRow(): void
    {
        // Given: 品名・金額・仕訳分類・区分が全て入力された1行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['コーヒー'], ['500'], ['3'], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 検証済みの行が1件返り、エラーはない
        $this->assertCount(1, $result['rows']);
        $this->assertSame([], $result['errors']);

        /** @var HouseholdAccountRecordRow $row */
        $row = $result['rows'][0];
        $this->assertSame('コーヒー', $row->getItemName());
        $this->assertSame(500, $row->getAmount());
        $this->assertSame(3, $row->getJournalCategoryId());
        $this->assertSame(HouseholdAccountRecord::TYPE_EXPENSE, $row->getType());
    }

    public function testParsesMultipleValidRows(): void
    {
        // Given: 複数の正しい行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(
            ['コーヒー', '書籍'],
            ['500', '1200'],
            ['3', '5'],
            [HouseholdAccountRecord::TYPE_EXPENSE, HouseholdAccountRecord::TYPE_EXPENSE]
        );

        // Then: 2件とも保存対象になる
        $this->assertCount(2, $result['rows']);
        $this->assertSame([], $result['errors']);
        $this->assertSame('コーヒー', $result['rows'][0]->getItemName());
        $this->assertSame('書籍', $result['rows'][1]->getItemName());
    }

    public function testSkipsFullyEmptyRowAmongMultipleRows(): void
    {
        // Given: 1行目は入力済み、2行目は完全に空
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(
            ['コーヒー', ''],
            ['500', ''],
            ['3', ''],
            [HouseholdAccountRecord::TYPE_EXPENSE, HouseholdAccountRecord::TYPE_EXPENSE]
        );

        // Then: 空行は無視され、1件だけ保存対象になる
        $this->assertCount(1, $result['rows']);
        $this->assertSame([], $result['errors']);
    }

    public function testSkipsFullyEmptyRowEvenWhenTypeIsFilled(): void
    {
        // Given: 品名・金額・仕訳分類は空だが、区分だけ初期値で入っている行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse([''], [''], [''], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 区分のみの入力は空行として無視される
        $this->assertSame([], $result['rows']);
        $this->assertSame([], $result['errors']);
    }

    public function testReturnsErrorWhenItemNameIsMissing(): void
    {
        // Given: 品名が空だが金額・仕訳分類は入力されている行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse([''], ['500'], ['3'], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 保存対象にならず、品名エラーが返る
        $this->assertSame([], $result['rows']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('品名', $result['errors'][0]);
    }

    public function testReturnsErrorWhenAmountIsNonNumeric(): void
    {
        // Given: 金額が数値でない行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['コーヒー'], ['abc'], ['3'], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 保存対象にならず、金額エラーが返る
        $this->assertSame([], $result['rows']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('金額', $result['errors'][0]);
    }

    public function testReturnsErrorWhenAmountIsZero(): void
    {
        // Given: 金額が0の行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['コーヒー'], ['0'], ['3'], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 保存対象にならず、金額エラーが返る
        $this->assertSame([], $result['rows']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('金額', $result['errors'][0]);
    }

    public function testReturnsErrorWhenAmountIsNegative(): void
    {
        // Given: 金額が負の値の行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['コーヒー'], ['-100'], ['3'], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 保存対象にならず、金額エラーが返る（先頭の "-" は数字ではないため）
        $this->assertSame([], $result['rows']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('金額', $result['errors'][0]);
    }

    public function testReturnsErrorWhenJournalCategoryIdIsMissing(): void
    {
        // Given: 仕訳分類が未選択の行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['コーヒー'], ['500'], [''], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 保存対象にならず、仕訳分類エラーが返る
        $this->assertSame([], $result['rows']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('仕訳分類', $result['errors'][0]);
    }

    public function testAccumulatesErrorsFromMultipleInvalidFields(): void
    {
        // Given: 品名・金額・仕訳分類が全て不正な行（区分は有効）
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse([''], ['abc'], [''], [HouseholdAccountRecord::TYPE_EXPENSE]);

        // Then: 3件のエラーが返る
        $this->assertSame([], $result['rows']);
        $this->assertCount(3, $result['errors']);
    }

    public function testValidAndInvalidRowsAreHandledIndependently(): void
    {
        // Given: 1行目は正しい入力、2行目は金額が不正
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(
            ['コーヒー', '書籍'],
            ['500', 'abc'],
            ['3', '5'],
            [HouseholdAccountRecord::TYPE_EXPENSE, HouseholdAccountRecord::TYPE_EXPENSE]
        );

        // Then: 正しい行のみ保存対象になり、不正な行はエラーとして扱われる
        $this->assertCount(1, $result['rows']);
        $this->assertSame('コーヒー', $result['rows'][0]->getItemName());
        $this->assertNotEmpty($result['errors']);
    }

    public function testParsesRowWithIncomeType(): void
    {
        // Given: 区分に「収入」が指定された行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['給与'], ['300000'], ['3'], [HouseholdAccountRecord::TYPE_INCOME]);

        // Then: 区分「収入」で保存対象になる
        $this->assertCount(1, $result['rows']);
        $this->assertSame([], $result['errors']);
        $this->assertSame(HouseholdAccountRecord::TYPE_INCOME, $result['rows'][0]->getType());
    }

    public function testTreatsUnspecifiedTypeAsExpense(): void
    {
        // Given: 区分が未指定（空文字）の有効な行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['コーヒー'], ['500'], ['3'], ['']);

        // Then: 区分は「支出」として扱われる
        $this->assertCount(1, $result['rows']);
        $this->assertSame([], $result['errors']);
        $this->assertSame(HouseholdAccountRecord::TYPE_EXPENSE, $result['rows'][0]->getType());
    }

    public function testTreatsMissingTypeEntryAsExpense(): void
    {
        // Given: types 配列自体に該当インデックスが存在しない行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする（types は空配列）
        $result = $parser->parse(['コーヒー'], ['500'], ['3'], []);

        // Then: 区分は「支出」として扱われる
        $this->assertCount(1, $result['rows']);
        $this->assertSame([], $result['errors']);
        $this->assertSame(HouseholdAccountRecord::TYPE_EXPENSE, $result['rows'][0]->getType());
    }

    public function testReturnsErrorWhenTypeIsInvalid(): void
    {
        // Given: 区分が「支出」「収入」以外の行
        $parser = new HouseholdAccountRecordRowParser();

        // When: パースする
        $result = $parser->parse(['コーヒー'], ['500'], ['3'], ['不正な区分']);

        // Then: 保存対象にならず、区分エラーが返る
        $this->assertSame([], $result['rows']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('区分', $result['errors'][0]);
    }
}
