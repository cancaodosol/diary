<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230621134025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note_tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unitary_note_note_tags (unitary_note_id INT NOT NULL, note_tags_id INT NOT NULL, INDEX IDX_97B9CB8A30588C6 (unitary_note_id), INDEX IDX_97B9CB8ACDD8B2E (note_tags_id), PRIMARY KEY(unitary_note_id, note_tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE unitary_note_note_tags ADD CONSTRAINT FK_97B9CB8A30588C6 FOREIGN KEY (unitary_note_id) REFERENCES unitary_note (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unitary_note_note_tags ADD CONSTRAINT FK_97B9CB8ACDD8B2E FOREIGN KEY (note_tags_id) REFERENCES note_tags (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unitary_note_note_tags DROP FOREIGN KEY FK_97B9CB8A30588C6');
        $this->addSql('ALTER TABLE unitary_note_note_tags DROP FOREIGN KEY FK_97B9CB8ACDD8B2E');
        $this->addSql('DROP TABLE note_tags');
        $this->addSql('DROP TABLE unitary_note_note_tags');
    }
}
