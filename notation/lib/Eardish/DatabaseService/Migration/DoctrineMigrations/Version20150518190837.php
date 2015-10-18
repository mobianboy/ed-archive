<?php

namespace Eardish\DatabaseService\Migration\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150518190837 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE badge ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE badge ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE badge ADD type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE badge ADD awarded_to VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE badge DROP badge_name');
        $this->addSql('ALTER TABLE badge DROP badge_type');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE badge ADD badge_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE badge ADD badge_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE badge DROP name');
        $this->addSql('ALTER TABLE badge DROP description');
        $this->addSql('ALTER TABLE badge DROP type');
        $this->addSql('ALTER TABLE badge DROP awarded_to');
    }
}
