<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604100614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA719EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA719EB6921 ON event (client_id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, DROP role, CHANGE email email VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA719EB6921');
        $this->addSql('DROP INDEX IDX_3BAE0AA719EB6921 ON event');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, DROP roles, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
