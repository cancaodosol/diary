<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * household_account_record に区分（支出/収入）カラムを追加する。
 * NOT NULL DEFAULT '支出' により、既存データも登録・表示処理で「支出」として扱える。
 */
final class Version20260925020000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add type column to household_account_record';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE household_account_record ADD type VARCHAR(16) DEFAULT '支出' NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE household_account_record DROP type');
    }
}
