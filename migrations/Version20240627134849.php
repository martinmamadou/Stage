<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627134849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE NoteFrais (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, employe_id INT NOT NULL, titre VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, prix_ht DOUBLE PRECISION DEFAULT NULL, quantite INT DEFAULT NULL, km DOUBLE PRECISION DEFAULT NULL, prix_km DOUBLE PRECISION DEFAULT NULL, total_ttc DOUBLE PRECISION NOT NULL, INDEX IDX_8B27C52B19EB6921 (client_id), INDEX IDX_8B27C52B1B65292 (employe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE NoteFrais ADD CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE NoteFrais ADD CONSTRAINT FK_8B27C52B1B65292 FOREIGN KEY (employe_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE NoteFrais DROP FOREIGN KEY FK_8B27C52B19EB6921');
        $this->addSql('ALTER TABLE NoteFrais DROP FOREIGN KEY FK_8B27C52B1B65292');
        $this->addSql('DROP TABLE NoteFrais');
    }
}
