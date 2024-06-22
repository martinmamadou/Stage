<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240613131802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee_movement ADD site_id INT NOT NULL');
        $this->addSql('ALTER TABLE employee_movement ADD CONSTRAINT FK_64E2C430F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_64E2C430F6BD1646 ON employee_movement (site_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee_movement DROP FOREIGN KEY FK_64E2C430F6BD1646');
        $this->addSql('DROP INDEX IDX_64E2C430F6BD1646 ON employee_movement');
        $this->addSql('ALTER TABLE employee_movement DROP site_id');
    }
}
