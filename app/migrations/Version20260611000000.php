<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260611000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'note_tags に表示色(display_color)と並び順(sort_order)カラムを追加';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note_tags ADD display_color VARCHAR(32) DEFAULT NULL, ADD sort_order INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note_tags DROP display_color, DROP sort_order');
    }
}
