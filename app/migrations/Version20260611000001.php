<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260611000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'note_tags に表示タイプ(display_type)カラムを追加。既存行の初期値は title（タイトル表示）';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE note_tags ADD display_type VARCHAR(32) NOT NULL DEFAULT 'title'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note_tags DROP display_type');
    }
}
