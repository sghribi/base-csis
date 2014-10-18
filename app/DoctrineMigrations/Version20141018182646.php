<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141018182646 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE laboratory DROP FOREIGN KEY FK_FDC719A8BBA16F66");
        $this->addSql("ALTER TABLE laboratory DROP FOREIGN KEY FK_FDC719A8602AD315");
        $this->addSql("DROP INDEX IDX_FDC719A8602AD315 ON laboratory");
        $this->addSql("DROP INDEX IDX_FDC719A8BBA16F66 ON laboratory");
        $this->addSql("ALTER TABLE laboratory DROP responsable_user_id, DROP responsible_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE laboratory ADD responsable_user_id INT DEFAULT NULL, ADD responsible_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE laboratory ADD CONSTRAINT FK_FDC719A8BBA16F66 FOREIGN KEY (responsable_user_id) REFERENCES user (id)");
        $this->addSql("ALTER TABLE laboratory ADD CONSTRAINT FK_FDC719A8602AD315 FOREIGN KEY (responsible_id) REFERENCES people (id)");
        $this->addSql("CREATE INDEX IDX_FDC719A8602AD315 ON laboratory (responsible_id)");
        $this->addSql("CREATE INDEX IDX_FDC719A8BBA16F66 ON laboratory (responsable_user_id)");
    }
}
