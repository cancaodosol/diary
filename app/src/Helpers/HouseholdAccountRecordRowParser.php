<?php

namespace App\Helpers;

use App\Entity\HouseholdAccountRecord;
use App\ValueObject\HouseholdAccountRecordRow;

/**
 * 家計簿入力欄（品名・金額・仕訳分類・区分）の複数行をリクエスト配列から取り出し、
 * 保存可能な値かどうかを検証する。DB アクセスは行わない。
 */
class HouseholdAccountRecordRowParser
{
    /**
     * 全項目が空の行は無視し、一部でも入力がある行は品名・金額・仕訳分類ID・区分の
     * 形式を検証する。仕訳分類IDの実在確認は呼び出し側（DBアクセス可能な層）が行う。
     * 区分が未指定の行は「支出」として扱う。
     *
     * @param string[] $itemNames
     * @param string[] $amounts
     * @param string[] $journalCategoryIds
     * @param string[] $types
     * @return array{rows: HouseholdAccountRecordRow[], errors: string[]}
     */
    public function parse(array $ids, array $itemNames, array $amounts, array $journalCategoryIds, array $types): array
    {
        $rowCount = max(count($ids), count($itemNames), count($amounts), count($journalCategoryIds));

        $rows = [];
        $errors = [];

        for ($i = 0; $i < $rowCount; $i++) {
            $id = (int) $ids[$i] ?? null;
            $itemName = trim((string) ($itemNames[$i] ?? ''));
            $amountRaw = trim((string) ($amounts[$i] ?? ''));
            $journalCategoryIdRaw = trim((string) ($journalCategoryIds[$i] ?? ''));
            $typeRaw = trim((string) ($types[$i] ?? ''));

            if ($id === '' && $itemName === '' && $amountRaw === '' && $journalCategoryIdRaw === '') {
                // 全項目未入力の行は保存対象外として無視する（区分のみの入力は無視）
                continue;
            }

            $type = $typeRaw === '' ? HouseholdAccountRecord::TYPE_EXPENSE : $typeRaw;

            $rowErrors = $this->validateRow($itemName, $amountRaw, $journalCategoryIdRaw, $type);
            if ($rowErrors !== []) {
                $errors = array_merge($errors, $rowErrors);
                continue;
            }

            $rows[] = new HouseholdAccountRecordRow($id, $itemName, (int) $amountRaw, (int) $journalCategoryIdRaw, $type);
        }

        return ['rows' => $rows, 'errors' => $errors];
    }

    /**
     * @return string[]
     */
    private function validateRow(string $itemName, string $amountRaw, string $journalCategoryIdRaw, string $type): array
    {
        $errors = [];

        if ($itemName === '') {
            $errors[] = '家計簿の品名を入力してください。';
        }

        if (!ctype_digit($amountRaw) || (int) $amountRaw <= 0) {
            $errors[] = '家計簿の金額は1以上の整数で入力してください。';
        }

        if (!ctype_digit($journalCategoryIdRaw)) {
            $errors[] = '家計簿の仕訳分類を選択してください。';
        }

        if (!in_array($type, HouseholdAccountRecord::TYPES, true)) {
            $errors[] = '家計簿の区分は支出または収入を選択してください。';
        }

        return $errors;
    }
}
