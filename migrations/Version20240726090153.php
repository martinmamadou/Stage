<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240726090153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE NoteFrais ADD forfait_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE NoteFrais ADD CONSTRAINT FK_8B27C52B906D5F2C FOREIGN KEY (forfait_id) REFERENCES forfait (id)');
        $this->addSql('CREATE INDEX IDX_8B27C52B906D5F2C ON NoteFrais (forfait_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE NoteFrais DROP FOREIGN KEY FK_8B27C52B906D5F2C');
        $this->addSql('DROP INDEX IDX_8B27C52B906D5F2C ON NoteFrais');
        $this->addSql('ALTER TABLE NoteFrais DROP forfait_id');
    }
}
