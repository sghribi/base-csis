<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141020191527 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE equipment_people DROP FOREIGN KEY FK_8F9815663147C936");
        $this->addSql("DROP TABLE equipment_people");
        $this->addSql("DROP TABLE people");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE equipment_people (equipment_id INT NOT NULL, people_id INT NOT NULL, INDEX IDX_8F981566517FE9FE (equipment_id), INDEX IDX_8F9815663147C936 (people_id), PRIMARY KEY(equipment_id, people_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE people (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phoneNumber VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_28166A26E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE equipment_people ADD CONSTRAINT FK_8F9815663147C936 FOREIGN KEY (people_id) REFERENCES people (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE equipment_people ADD CONSTRAINT FK_8F981566517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE");
    }
}
