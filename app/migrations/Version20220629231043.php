<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220629231043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "refresh_tokens_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE board (id UUID NOT NULL, gallery_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, orientation VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_58562B474E7AF8F ON board (gallery_id)');
        $this->addSql('COMMENT ON COLUMN board.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN board.gallery_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE exhibition (id UUID NOT NULL, revision_id UUID DEFAULT NULL, work_id UUID NOT NULL, user_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, reaction BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B83533891DFA7C8F ON exhibition (revision_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8353389BB3453DB ON exhibition (work_id)');
        $this->addSql('CREATE INDEX IDX_B8353389A76ED395 ON exhibition (user_id)');
        $this->addSql('COMMENT ON COLUMN exhibition.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition.revision_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition.work_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE exhibition_statut (id UUID NOT NULL, exhibition_id UUID NOT NULL, status VARCHAR(255) NOT NULL, decription VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_32309C9D2A7D4494 ON exhibition_statut (exhibition_id)');
        $this->addSql('COMMENT ON COLUMN exhibition_statut.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition_statut.exhibition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE exhibition_statut_user (exhibition_statut_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(exhibition_statut_id, user_id))');
        $this->addSql('CREATE INDEX IDX_C7ADAA0E68A71B1B ON exhibition_statut_user (exhibition_statut_id)');
        $this->addSql('CREATE INDEX IDX_C7ADAA0EA76ED395 ON exhibition_statut_user (user_id)');
        $this->addSql('COMMENT ON COLUMN exhibition_statut_user.exhibition_statut_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition_statut_user.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE gallery (id UUID NOT NULL, created_user_id UUID NOT NULL, name VARCHAR(255) NOT NULL, gps_lat NUMERIC(8, 6) NOT NULL, gps_long NUMERIC(9, 6) NOT NULL, price NUMERIC(5, 2) NOT NULL, max_days INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_472B783AE104C1D3 ON gallery (created_user_id)');
        $this->addSql('COMMENT ON COLUMN gallery.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN gallery.created_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "refresh_tokens" (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON "refresh_tokens" (refresh_token)');
        $this->addSql('CREATE TABLE reservation (id UUID NOT NULL, exhibition_id UUID NOT NULL, date_start DATE NOT NULL, duration INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42C849552A7D4494 ON reservation (exhibition_id)');
        $this->addSql('COMMENT ON COLUMN reservation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.exhibition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE work (id UUID NOT NULL, user_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_534E6880A76ED395 ON work (user_id)');
        $this->addSql('COMMENT ON COLUMN work.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN work.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE work_files (id UUID NOT NULL, work_id UUID NOT NULL, path_file VARCHAR(255) NOT NULL, main BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2ECE3B76BB3453DB ON work_files (work_id)');
        $this->addSql('COMMENT ON COLUMN work_files.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN work_files.work_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B474E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition ADD CONSTRAINT FK_B83533891DFA7C8F FOREIGN KEY (revision_id) REFERENCES exhibition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition ADD CONSTRAINT FK_B8353389BB3453DB FOREIGN KEY (work_id) REFERENCES work (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition ADD CONSTRAINT FK_B8353389A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition_statut ADD CONSTRAINT FK_32309C9D2A7D4494 FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition_statut_user ADD CONSTRAINT FK_C7ADAA0E68A71B1B FOREIGN KEY (exhibition_statut_id) REFERENCES exhibition_statut (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition_statut_user ADD CONSTRAINT FK_C7ADAA0EA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AE104C1D3 FOREIGN KEY (created_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552A7D4494 FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E6880A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_files ADD CONSTRAINT FK_2ECE3B76BB3453DB FOREIGN KEY (work_id) REFERENCES work (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE exhibition DROP CONSTRAINT FK_B83533891DFA7C8F');
        $this->addSql('ALTER TABLE exhibition_statut DROP CONSTRAINT FK_32309C9D2A7D4494');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849552A7D4494');
        $this->addSql('ALTER TABLE exhibition_statut_user DROP CONSTRAINT FK_C7ADAA0E68A71B1B');
        $this->addSql('ALTER TABLE board DROP CONSTRAINT FK_58562B474E7AF8F');
        $this->addSql('ALTER TABLE exhibition DROP CONSTRAINT FK_B8353389A76ED395');
        $this->addSql('ALTER TABLE exhibition_statut_user DROP CONSTRAINT FK_C7ADAA0EA76ED395');
        $this->addSql('ALTER TABLE gallery DROP CONSTRAINT FK_472B783AE104C1D3');
        $this->addSql('ALTER TABLE work DROP CONSTRAINT FK_534E6880A76ED395');
        $this->addSql('ALTER TABLE exhibition DROP CONSTRAINT FK_B8353389BB3453DB');
        $this->addSql('ALTER TABLE work_files DROP CONSTRAINT FK_2ECE3B76BB3453DB');
        $this->addSql('DROP SEQUENCE "refresh_tokens_id_seq" CASCADE');
        $this->addSql('DROP TABLE board');
        $this->addSql('DROP TABLE exhibition');
        $this->addSql('DROP TABLE exhibition_statut');
        $this->addSql('DROP TABLE exhibition_statut_user');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE "refresh_tokens"');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE work');
        $this->addSql('DROP TABLE work_files');
    }
}
