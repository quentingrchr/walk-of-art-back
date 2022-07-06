<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220706144559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_b8353389bb3453db');
        $this->addSql('ALTER TABLE exhibition ADD comment BOOLEAN NOT NULL');
        $this->addSql('CREATE INDEX IDX_B8353389BB3453DB ON exhibition (work_id)');
        $this->addSql('ALTER TABLE gallery DROP CONSTRAINT fk_472b783ae104c1d3');
        $this->addSql('DROP INDEX idx_472b783ae104c1d3');
        $this->addSql('ALTER TABLE gallery ADD latitude DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE gallery ADD longitude DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE gallery DROP gps_lat');
        $this->addSql('ALTER TABLE gallery DROP gps_long');
        $this->addSql('ALTER TABLE gallery RENAME COLUMN created_user_id TO user_id');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_472B783AA76ED395 ON gallery (user_id)');
        $this->addSql('ALTER TABLE reservation ADD date_end DATE NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP duration');
        $this->addSql('ALTER TABLE work ADD main_file_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN work.main_file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E68806780D085 FOREIGN KEY (main_file_id) REFERENCES work_files (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_534E68806780D085 ON work (main_file_id)');
        $this->addSql('ALTER TABLE work_files ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE work_files DROP main');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE gallery DROP CONSTRAINT FK_472B783AA76ED395');
        $this->addSql('DROP INDEX IDX_472B783AA76ED395');
        $this->addSql('ALTER TABLE gallery ADD gps_lat NUMERIC(8, 6) NOT NULL');
        $this->addSql('ALTER TABLE gallery ADD gps_long NUMERIC(9, 6) NOT NULL');
        $this->addSql('ALTER TABLE gallery DROP latitude');
        $this->addSql('ALTER TABLE gallery DROP longitude');
        $this->addSql('ALTER TABLE gallery RENAME COLUMN user_id TO created_user_id');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT fk_472b783ae104c1d3 FOREIGN KEY (created_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_472b783ae104c1d3 ON gallery (created_user_id)');
        $this->addSql('DROP INDEX IDX_B8353389BB3453DB');
        $this->addSql('ALTER TABLE exhibition DROP comment');
        $this->addSql('CREATE UNIQUE INDEX uniq_b8353389bb3453db ON exhibition (work_id)');
        $this->addSql('ALTER TABLE work DROP CONSTRAINT FK_534E68806780D085');
        $this->addSql('DROP INDEX UNIQ_534E68806780D085');
        $this->addSql('ALTER TABLE work DROP main_file_id');
        $this->addSql('ALTER TABLE reservation ADD duration INT NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP date_end');
        $this->addSql('ALTER TABLE work_files ADD main BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE work_files DROP created_at');
    }
}
