<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712165439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exhibition_status (id UUID NOT NULL, exhibition_id UUID NOT NULL, user_id UUID NOT NULL, status VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC54093E2A7D4494 ON exhibition_status (exhibition_id)');
        $this->addSql('CREATE INDEX IDX_AC54093EA76ED395 ON exhibition_status (user_id)');
        $this->addSql('COMMENT ON COLUMN exhibition_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition_status.exhibition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition_status.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exhibition_status ADD CONSTRAINT FK_AC54093E2A7D4494 FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition_status ADD CONSTRAINT FK_AC54093EA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE exhibition_statut');
        $this->addSql('ALTER TABLE work ALTER updated_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE exhibition_statut (id UUID NOT NULL, exhibition_id UUID NOT NULL, user_id UUID NOT NULL, status VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_32309c9d2a7d4494 ON exhibition_statut (exhibition_id)');
        $this->addSql('CREATE INDEX idx_32309c9da76ed395 ON exhibition_statut (user_id)');
        $this->addSql('COMMENT ON COLUMN exhibition_statut.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition_statut.exhibition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition_statut.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exhibition_statut ADD CONSTRAINT fk_32309c9d2a7d4494 FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition_statut ADD CONSTRAINT fk_32309c9da76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE exhibition_status');
        $this->addSql('ALTER TABLE work ALTER updated_at DROP NOT NULL');
    }
}
