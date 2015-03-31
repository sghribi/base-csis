<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150331153640 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE institution (id INT AUTO_INCREMENT NOT NULL, acronym VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, lastEditDate DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE institution_user (institution_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9B90060210405986 (institution_id), INDEX IDX_9B900602A76ED395 (user_id), PRIMARY KEY(institution_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, laboratory_id INT NOT NULL, designation VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, building VARCHAR(255) DEFAULT NULL, floor VARCHAR(255) DEFAULT NULL, room VARCHAR(255) DEFAULT NULL, shared INT NOT NULL, brand VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, lastEditDate DATETIME NOT NULL, url VARCHAR(255) DEFAULT NULL, INDEX IDX_D338D5832F2A371E (laboratory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment_user (equipment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B717074F517FE9FE (equipment_id), INDEX IDX_B717074FA76ED395 (user_id), PRIMARY KEY(equipment_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE laboratory (id INT AUTO_INCREMENT NOT NULL, institution_id INT NOT NULL, acronym VARCHAR(255) NOT NULL, nameFr VARCHAR(255) NOT NULL, nameEn VARCHAR(255) NOT NULL, researchLaboratory TINYINT(1) NOT NULL, lastEditDate DATETIME NOT NULL, INDEX IDX_FDC719A810405986 (institution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE laboratory_user (laboratory_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_880275252F2A371E (laboratory_id), INDEX IDX_88027525A76ED395 (user_id), PRIMARY KEY(laboratory_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, tag VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, lastEditDate DATETIME NOT NULL, UNIQUE INDEX UNIQ_389B783389B783 (tag), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment_tag (id INT AUTO_INCREMENT NOT NULL, equipment_id INT NOT NULL, tag_id INT NOT NULL, status INT NOT NULL, INDEX IDX_32097FE2517FE9FE (equipment_id), INDEX IDX_32097FE2BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, lab_id INT DEFAULT NULL, institution_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), INDEX IDX_8D93D649628913D5 (lab_id), INDEX IDX_8D93D64910405986 (institution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE institution_user ADD CONSTRAINT FK_9B90060210405986 FOREIGN KEY (institution_id) REFERENCES institution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE institution_user ADD CONSTRAINT FK_9B900602A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D5832F2A371E FOREIGN KEY (laboratory_id) REFERENCES laboratory (id)');
        $this->addSql('ALTER TABLE equipment_user ADD CONSTRAINT FK_B717074F517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment_user ADD CONSTRAINT FK_B717074FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE laboratory ADD CONSTRAINT FK_FDC719A810405986 FOREIGN KEY (institution_id)REFERENCES institution (id)');
        $this->addSql('ALTER TABLE laboratory_user ADD CONSTRAINT FK_880275252F2A371E FOREIGN KEY (laboratory_id) REFERENCES laboratory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE laboratory_user ADD CONSTRAINT FK_88027525A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment_tag ADD CONSTRAINT FK_32097FE2517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE equipment_tag ADD CONSTRAINT FK_32097FE2BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649628913D5 FOREIGN KEY (lab_id) REFERENCES laboratory (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64910405986 FOREIGN KEY (institution_id) REFERENCES institution (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE institution_user DROP FOREIGN KEY FK_9B90060210405986');
        $this->addSql('ALTER TABLE laboratory DROP FOREIGN KEY FK_FDC719A810405986');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64910405986');
        $this->addSql('ALTER TABLE equipment_user DROP FOREIGN KEY FK_B717074F517FE9FE');
        $this->addSql('ALTER TABLE equipment_tag DROP FOREIGN KEY FK_32097FE2517FE9FE');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D5832F2A371E');
        $this->addSql('ALTER TABLE laboratory_user DROP FOREIGN KEY FK_880275252F2A371E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649628913D5');
        $this->addSql('ALTER TABLE equipment_tag DROP FOREIGN KEY FK_32097FE2BAD26311');
        $this->addSql('ALTER TABLE institution_user DROP FOREIGN KEY FK_9B900602A76ED395');
        $this->addSql('ALTER TABLE equipment_user DROP FOREIGN KEY FK_B717074FA76ED395');
        $this->addSql('ALTER TABLE laboratory_user DROP FOREIGN KEY FK_88027525A76ED395');
        $this->addSql('DROP TABLE institution');
        $this->addSql('DROP TABLE institution_user');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE equipment_user');
        $this->addSql('DROP TABLE laboratory');
        $this->addSql('DROP TABLE laboratory_user');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE equipment_tag');
        $this->addSql('DROP TABLE user');
    }
}
