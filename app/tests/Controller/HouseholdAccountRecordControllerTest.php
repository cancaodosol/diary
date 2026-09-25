<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * 家計簿一覧画面への到達経路（route）を検証する。
 * 一覧取得はDBアクセスを伴うため、DB接続なしで検証できるルーティング層に限定する。
 */
class HouseholdAccountRecordControllerTest extends KernelTestCase
{
    public function testListRouteIsRegisteredAndReachable(): void
    {
        // Given: アプリケーションのルーティング設定
        self::bootKernel();
        $router = self::getContainer()->get('router');

        // When: 家計簿一覧画面のルート名からURLを生成する
        $path = $router->generate('app_household_account_records');

        // Then: 期待するパスが解決される
        $this->assertSame('/household/account-records', $path);
    }

    public function testBaseTemplateLinksToHouseholdAccountRecordList(): void
    {
        // Given: 全画面共通ナビゲーションのテンプレート
        $template = file_get_contents(dirname(__DIR__, 2) . '/templates/base.html.twig');

        // Then: 家計簿一覧画面への導線が存在する
        $this->assertStringContainsString("path('app_household_account_records')", $template);
    }
}
