<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715104431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reaction (id UUID NOT NULL, exhibition_id UUID NOT NULL, reaction VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, visitor VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A4D707F72A7D4494 ON reaction (exhibition_id)');
        $this->addSql('COMMENT ON COLUMN reaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reaction.exhibition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F72A7D4494 FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE reaction');
    }
}
