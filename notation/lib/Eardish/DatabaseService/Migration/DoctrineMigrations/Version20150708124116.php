<?php

namespace Eardish\DatabaseService\Migration\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150708124116 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE profile ADD ar_rep INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD last_edited_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F7DEC83E7 FOREIGN KEY (ar_rep) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FC16AD6BA FOREIGN KEY (last_edited_by) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8157AA0F7DEC83E7 ON profile (ar_rep)');
        $this->addSql('CREATE INDEX IDX_8157AA0FC16AD6BA ON profile (last_edited_by)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE profile DROP CONSTRAINT FK_8157AA0F7DEC83E7');
        $this->addSql('ALTER TABLE profile DROP CONSTRAINT FK_8157AA0FC16AD6BA');
        $this->addSql('DROP INDEX IDX_8157AA0F7DEC83E7');
        $this->addSql('DROP INDEX IDX_8157AA0FC16AD6BA');
        $this->addSql('ALTER TABLE profile DROP ar_rep');
        $this->addSql('ALTER TABLE profile DROP last_edited_by');
    }
}
