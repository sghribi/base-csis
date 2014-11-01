<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141101185229 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE equipment_category DROP FOREIGN KEY FK_368F9DE712469DE2");
        $this->addSql("DROP TABLE category");
        $this->addSql("DROP TABLE equipment_category");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE equipment_category (equipment_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_368F9DE7517FE9FE (equipment_id), INDEX IDX_368F9DE712469DE2 (category_id), PRIMARY KEY(equipment_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE equipment_category ADD CONSTRAINT FK_368F9DE712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE equipment_category ADD CONSTRAINT FK_368F9DE7517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE");
    }
}
