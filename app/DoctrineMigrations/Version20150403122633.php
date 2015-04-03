<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150403122633 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE equipment_tag DROP id, DROP status');
        $this->addSql('SET session old_alter_table=1');
        $this->addSql('ALTER IGNORE TABLE equipment_tag ADD UNIQUE INDEX `equipment_tag_dbl` (equipment_id, tag_id)');
        $this->addSql('ALTER TABLE equipment_tag DROP INDEX `equipment_tag_dbl`');
        $this->addSql('SET session old_alter_table=0');
        $this->addSql('ALTER TABLE equipment_tag ADD PRIMARY KEY (equipment_id, tag_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE equipment_tag ADD id INT AUTO_INCREMENT NOT NULL, ADD status INT NOT NULL');
        $this->addSql('ALTER TABLE equipment_tag ADD PRIMARY KEY (id)');
    }
}
