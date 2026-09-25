<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * 家計簿レコード（household_account_record）と仕訳分類（journal_category）テーブルを追加する。
 */
final class Version20260925011448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add household_account_record and journal_category tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE household_account_record (id INT AUTO_INCREMENT NOT NULL, unitary_note_id INT NOT NULL, journal_category_id INT NOT NULL, `date` DATE NOT NULL, item_name VARCHAR(255) NOT NULL, amount INT NOT NULL, INDEX IDX_E0A5F28430588C6 (unitary_note_id), INDEX IDX_E0A5F2848012985 (journal_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE journal_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE household_account_record ADD CONSTRAINT FK_E0A5F28430588C6 FOREIGN KEY (unitary_note_id) REFERENCES unitary_note (id)');
        $this->addSql('ALTER TABLE household_account_record ADD CONSTRAINT FK_E0A5F2848012985 FOREIGN KEY (journal_category_id) REFERENCES journal_category (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE household_account_record DROP FOREIGN KEY FK_E0A5F28430588C6');
        $this->addSql('ALTER TABLE household_account_record DROP FOREIGN KEY FK_E0A5F2848012985');
        $this->addSql('DROP TABLE household_account_record');
        $this->addSql('DROP TABLE journal_category');
    }
}
