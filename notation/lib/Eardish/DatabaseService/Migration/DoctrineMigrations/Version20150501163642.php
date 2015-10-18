<?php

namespace Eardish\DatabaseService\Migration\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150501163642 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE analytic ADD artist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE analytic ADD CONSTRAINT FK_18CBC1D1B7970CF8 FOREIGN KEY (artist_id) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_18CBC1D1B7970CF8 ON analytic (artist_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE analytic DROP CONSTRAINT FK_18CBC1D1B7970CF8');
        $this->addSql('DROP INDEX IDX_18CBC1D1B7970CF8');
        $this->addSql('ALTER TABLE analytic DROP artist_id');
    }
}
