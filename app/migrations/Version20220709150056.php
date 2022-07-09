<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220709150056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE exhibition_statut_user');
        $this->addSql('ALTER TABLE exhibition_statut ADD user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE exhibition_statut RENAME COLUMN decription TO description');
        $this->addSql('COMMENT ON COLUMN exhibition_statut.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exhibition_statut ADD CONSTRAINT FK_32309C9DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_32309C9DA76ED395 ON exhibition_statut (user_id)');
        $this->addSql('ALTER TABLE gallery DROP CONSTRAINT fk_472b783aa76ed395');
        $this->addSql('DROP INDEX idx_472b783aa76ed395');
        $this->addSql('ALTER TABLE gallery DROP user_id');
        $this->addSql('ALTER TABLE gallery ALTER price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE gallery ALTER price DROP DEFAULT');
        $this->addSql('ALTER TABLE reservation ADD board_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN reservation.board_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C84955E7EC5785 ON reservation (board_id)');
        $this->addSql('ALTER TABLE "user" ADD url_facebook VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD url_twitter VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD url_personal_website VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE exhibition_statut_user (exhibition_statut_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(exhibition_statut_id, user_id))');
        $this->addSql('CREATE INDEX idx_c7adaa0ea76ed395 ON exhibition_statut_user (user_id)');
        $this->addSql('CREATE INDEX idx_c7adaa0e68a71b1b ON exhibition_statut_user (exhibition_statut_id)');
        $this->addSql('COMMENT ON COLUMN exhibition_statut_user.exhibition_statut_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN exhibition_statut_user.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exhibition_statut_user ADD CONSTRAINT fk_c7adaa0e68a71b1b FOREIGN KEY (exhibition_statut_id) REFERENCES exhibition_statut (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exhibition_statut_user ADD CONSTRAINT fk_c7adaa0ea76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP url_facebook');
        $this->addSql('ALTER TABLE "user" DROP url_twitter');
        $this->addSql('ALTER TABLE "user" DROP url_personal_website');
        $this->addSql('ALTER TABLE exhibition_statut DROP CONSTRAINT FK_32309C9DA76ED395');
        $this->addSql('DROP INDEX IDX_32309C9DA76ED395');
        $this->addSql('ALTER TABLE exhibition_statut DROP user_id');
        $this->addSql('ALTER TABLE exhibition_statut RENAME COLUMN description TO decription');
        $this->addSql('ALTER TABLE gallery ADD user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE gallery ALTER price TYPE NUMERIC(5, 2)');
        $this->addSql('ALTER TABLE gallery ALTER price DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN gallery.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT fk_472b783aa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_472b783aa76ed395 ON gallery (user_id)');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955E7EC5785');
        $this->addSql('DROP INDEX IDX_42C84955E7EC5785');
        $this->addSql('ALTER TABLE reservation DROP board_id');
    }
}
