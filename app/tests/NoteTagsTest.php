<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use App\Entity\NoteTags;

class NoteTagsTest extends TestCase
{
    public function testNameGetterSetter(): void
    {
        // Given: 新規 NoteTags
        $tag = new NoteTags();

        // When: タグ名をセット
        $tag->setName('テストタグ');

        // Then: 同じ値が返る
        $this->assertSame('テストタグ', $tag->getName());
    }

    public function testDisplayColorGetterSetter(): void
    {
        // Given: 新規 NoteTags
        $tag = new NoteTags();

        // When: 表示色をセット
        $tag->setDisplayColor('#ff0000');

        // Then: 同じ値が返る
        $this->assertSame('#ff0000', $tag->getDisplayColor());
    }

    public function testDisplayColorAllowsNull(): void
    {
        // Given: 表示色が設定済みの NoteTags
        $tag = new NoteTags();
        $tag->setDisplayColor('#123456');

        // When: nullをセット
        $tag->setDisplayColor(null);

        // Then: nullが返る
        $this->assertNull($tag->getDisplayColor());
    }

    public function testSortOrderDefaultIsZero(): void
    {
        // Given: 新規 NoteTags（並び順未設定）
        $tag = new NoteTags();

        // Then: デフォルトは0
        $this->assertSame(0, $tag->getSortOrder());
    }

    public function testSortOrderGetterSetter(): void
    {
        // Given: 新規 NoteTags
        $tag = new NoteTags();

        // When: 並び順をセット
        $tag->setSortOrder(5);

        // Then: 同じ値が返る
        $this->assertSame(5, $tag->getSortOrder());
    }

    public function testParentTagIdAllowsNull(): void
    {
        // Given: 親IDが設定済みの NoteTags
        $tag = new NoteTags();
        $tag->setParentTagId(10);

        // When: nullをセット
        $tag->setParentTagId(null);

        // Then: nullが返る
        $this->assertNull($tag->getParentTagId());
    }

    public function testParentTagIdGetterSetter(): void
    {
        // Given: 新規 NoteTags
        $tag = new NoteTags();

        // When: 親IDをセット
        $tag->setParentTagId(3);

        // Then: 同じ値が返る
        $this->assertSame(3, $tag->getParentTagId());
    }

    public function testDescriptionGetterSetter(): void
    {
        // Given: 新規 NoteTags
        $tag = new NoteTags();

        // When: 説明をセット
        $tag->setDescription('テスト説明');

        // Then: 同じ値が返る
        $this->assertSame('テスト説明', $tag->getDescription());
    }

    public function testDisplayTypeDefaultIsTitle(): void
    {
        // Given: 新規 NoteTags（表示タイプ未設定）
        $tag = new NoteTags();

        // Then: デフォルトは「タイトル表示」
        $this->assertSame(NoteTags::DISPLAY_TYPE_TITLE, $tag->getDisplayType());
    }

    public function testDisplayTypeGetterSetter(): void
    {
        // Given: 新規 NoteTags
        $tag = new NoteTags();

        // When: 表示タイプを「画像表示」にセット
        $tag->setDisplayType(NoteTags::DISPLAY_TYPE_IMAGE);

        // Then: 同じ値が返る
        $this->assertSame(NoteTags::DISPLAY_TYPE_IMAGE, $tag->getDisplayType());
    }

    public function testDisplayTypeCanBeSetBackToTitle(): void
    {
        // Given: 画像表示に設定済みの NoteTags
        $tag = new NoteTags();
        $tag->setDisplayType(NoteTags::DISPLAY_TYPE_IMAGE);

        // When: タイトル表示に戻す
        $tag->setDisplayType(NoteTags::DISPLAY_TYPE_TITLE);

        // Then: タイトル表示が返る
        $this->assertSame(NoteTags::DISPLAY_TYPE_TITLE, $tag->getDisplayType());
    }

    public function testDisplayTypeValuesConstantContainsBothValues(): void
    {
        // Then: 定数 DISPLAY_TYPE_VALUES に title と image の両方が含まれる
        $this->assertContains(NoteTags::DISPLAY_TYPE_TITLE, NoteTags::DISPLAY_TYPE_VALUES);
        $this->assertContains(NoteTags::DISPLAY_TYPE_IMAGE, NoteTags::DISPLAY_TYPE_VALUES);
        $this->assertCount(2, NoteTags::DISPLAY_TYPE_VALUES);
    }

    public function testToArrayContainsAllRequiredFields(): void
    {
        // Given: 全フィールドが設定された NoteTags
        $tag = new NoteTags();
        $tag->setName('タグA');
        $tag->setDisplayColor('#00ff00');
        $tag->setDisplayType(NoteTags::DISPLAY_TYPE_IMAGE);
        $tag->setDescription('説明A');
        $tag->setSortOrder(2);
        $tag->setParentTagId(1);

        // When: toArray を呼ぶ
        $array = $tag->toArray();

        // Then: 全必須フィールドが含まれる
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('displayColor', $array);
        $this->assertArrayHasKey('displayType', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('sortOrder', $array);
        $this->assertArrayHasKey('parentTagId', $array);
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('textHtml', $array);

        $this->assertSame('タグA', $array['name']);
        $this->assertSame('#00ff00', $array['displayColor']);
        $this->assertSame(NoteTags::DISPLAY_TYPE_IMAGE, $array['displayType']);
        $this->assertSame('説明A', $array['description']);
        $this->assertSame(2, $array['sortOrder']);
        $this->assertSame(1, $array['parentTagId']);
    }

    public function testToArrayWithNullableFields(): void
    {
        // Given: オプション項目が未設定の NoteTags
        $tag = new NoteTags();
        $tag->setName('最小タグ');

        // When: toArray を呼ぶ
        $array = $tag->toArray();

        // Then: nullable フィールドは null が返り、displayType はデフォルト値が返る
        $this->assertNull($array['displayColor']);
        $this->assertNull($array['description']);
        $this->assertNull($array['parentTagId']);
        $this->assertSame(0, $array['sortOrder']);
        $this->assertSame(NoteTags::DISPLAY_TYPE_TITLE, $array['displayType']);
    }
}
