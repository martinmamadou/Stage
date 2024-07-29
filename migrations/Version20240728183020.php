<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240728183020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note_frais (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, employe_id INT NOT NULL, taxe_id INT DEFAULT NULL, forfait_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, categorie VARCHAR(255) DEFAULT NULL, prix_ht DOUBLE PRECISION DEFAULT NULL, quantite INT DEFAULT NULL, km DOUBLE PRECISION DEFAULT NULL, prix_km DOUBLE PRECISION DEFAULT NULL, total_ttc DOUBLE PRECISION NOT NULL, carte_client TINYINT(1) DEFAULT NULL, creation DATE DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4050EF4F19EB6921 (client_id), INDEX IDX_4050EF4F1B65292 (employe_id), INDEX IDX_4050EF4F1AB947A4 (taxe_id), INDEX IDX_4050EF4F906D5F2C (forfait_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE note_frais ADD CONSTRAINT FK_4050EF4F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE note_frais ADD CONSTRAINT FK_4050EF4F1B65292 FOREIGN KEY (employe_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE note_frais ADD CONSTRAINT FK_4050EF4F1AB947A4 FOREIGN KEY (taxe_id) REFERENCES taxe (id)');
        $this->addSql('ALTER TABLE note_frais ADD CONSTRAINT FK_4050EF4F906D5F2C FOREIGN KEY (forfait_id) REFERENCES forfait (id)');
        $this->addSql('ALTER TABLE NoteFrais DROP FOREIGN KEY FK_8B27C52B19EB6921');
        $this->addSql('ALTER TABLE NoteFrais DROP FOREIGN KEY FK_8B27C52B1AB947A4');
        $this->addSql('ALTER TABLE NoteFrais DROP FOREIGN KEY FK_8B27C52B1B65292');
        $this->addSql('ALTER TABLE NoteFrais DROP FOREIGN KEY FK_8B27C52B906D5F2C');
        $this->addSql('DROP TABLE NoteFrais');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE NoteFrais (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, employe_id INT NOT NULL, taxe_id INT DEFAULT NULL, forfait_id INT DEFAULT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, categorie VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, prix_ht DOUBLE PRECISION DEFAULT NULL, quantite INT DEFAULT NULL, km DOUBLE PRECISION DEFAULT NULL, total_ttc DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', carte_client TINYINT(1) DEFAULT NULL, creation DATE DEFAULT NULL, prix_km DOUBLE PRECISION DEFAULT NULL, INDEX IDX_8B27C52B19EB6921 (client_id), INDEX IDX_8B27C52B1B65292 (employe_id), INDEX IDX_8B27C52B1AB947A4 (taxe_id), INDEX IDX_8B27C52B906D5F2C (forfait_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE NoteFrais ADD CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE NoteFrais ADD CONSTRAINT FK_8B27C52B1AB947A4 FOREIGN KEY (taxe_id) REFERENCES taxe (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE NoteFrais ADD CONSTRAINT FK_8B27C52B1B65292 FOREIGN KEY (employe_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE NoteFrais ADD CONSTRAINT FK_8B27C52B906D5F2C FOREIGN KEY (forfait_id) REFERENCES forfait (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE note_frais DROP FOREIGN KEY FK_4050EF4F19EB6921');
        $this->addSql('ALTER TABLE note_frais DROP FOREIGN KEY FK_4050EF4F1B65292');
        $this->addSql('ALTER TABLE note_frais DROP FOREIGN KEY FK_4050EF4F1AB947A4');
        $this->addSql('ALTER TABLE note_frais DROP FOREIGN KEY FK_4050EF4F906D5F2C');
        $this->addSql('DROP TABLE note_frais');
    }
}
