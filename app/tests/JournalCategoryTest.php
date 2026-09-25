<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use App\Entity\JournalCategory;

class JournalCategoryTest extends TestCase
{
    public function testNameGetterSetter(): void
    {
        // Given: 新規 JournalCategory
        $category = new JournalCategory();

        // When: 分類名をセット
        $category->setName('食費');

        // Then: 同じ値が返る
        $this->assertSame('食費', $category->getName());
    }

    public function testIdIsNullBeforePersisting(): void
    {
        // Given: 新規 JournalCategory（永続化前）
        $category = new JournalCategory();

        // Then: IDはまだ採番されていない
        $this->assertNull($category->getId());
    }

    public function testSetNameReturnsSelfForChaining(): void
    {
        // Given: 新規 JournalCategory
        $category = new JournalCategory();

        // When: setName を呼ぶ
        $result = $category->setName('交通費');

        // Then: 自身のインスタンスが返る（メソッドチェーン可能）
        $this->assertSame($category, $result);
    }
}
