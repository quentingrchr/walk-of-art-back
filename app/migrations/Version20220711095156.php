<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711095156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE exhibition ADD board_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE exhibition ADD date_start DATE NOT NULL');
        $this->addSql('ALTER TABLE exhibition ADD date_end DATE NOT NULL');
        $this->addSql('COMMENT ON COLUMN exhibition.board_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exhibition ADD CONSTRAINT FK_B8353389E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B8353389E7EC5785 ON exhibition (board_id)');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN url_twitter TO url_instagram');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE reservation (id UUID NOT NULL, exhibition_id UUID NOT NULL, board_id UUID DEFAULT NULL, date_start DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_end DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_42c84955e7ec5785 ON reservation (board_id)');
        $this->addSql('CREATE INDEX idx_42c849552a7d4494 ON reservation (exhibition_id)');
        $this->addSql('COMMENT ON COLUMN reservation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.exhibition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.board_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c849552a7d4494 FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c84955e7ec5785 FOREIGN KEY (board_id) REFERENCES board (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition DROP CONSTRAINT FK_B8353389E7EC5785');
        $this->addSql('DROP INDEX IDX_B8353389E7EC5785');
        $this->addSql('ALTER TABLE exhibition DROP board_id');
        $this->addSql('ALTER TABLE exhibition DROP date_start');
        $this->addSql('ALTER TABLE exhibition DROP date_end');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN url_instagram TO url_twitter');
    }
}
