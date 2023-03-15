<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230310174052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE backend_language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, deleted TINYINT(1) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_C65490E25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(100) DEFAULT NULL, firstname VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, subject VARCHAR(255) DEFAULT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) DEFAULT NULL, is_answered TINYINT(1) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE frontend_language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, deleted TINYINT(1) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_1808EF535E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX IDX_E01FBE6A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) DEFAULT \'brouillon\' NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, project_link VARCHAR(255) DEFAULT NULL, github_link VARCHAR(255) DEFAULT NULL, year VARCHAR(255) DEFAULT NULL, last_update DATE DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(255) DEFAULT NULL, FULLTEXT INDEX IDX_5C93B3A45E237E066DE44026 (name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_frontend_language (project_id INT NOT NULL, frontend_language_id INT NOT NULL, INDEX IDX_2CDBB36C166D1F9C (project_id), INDEX IDX_2CDBB36CDF8632D0 (frontend_language_id), PRIMARY KEY(project_id, frontend_language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_backend_language (project_id INT NOT NULL, backend_language_id INT NOT NULL, INDEX IDX_7BD1538B166D1F9C (project_id), INDEX IDX_7BD1538BD160DECF (backend_language_id), PRIMARY KEY(project_id, backend_language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_tool (project_id INT NOT NULL, tool_id INT NOT NULL, INDEX IDX_1962F6C9166D1F9C (project_id), INDEX IDX_1962F6C98F7B22CC (tool_id), PRIMARY KEY(project_id, tool_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tool (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, deleted TINYINT(1) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_20F33ED15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, lastname VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, is_verified TINYINT(1) DEFAULT NULL, reset_token VARCHAR(100) DEFAULT NULL, count_session INT DEFAULT NULL, last_login DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed DATETIME NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE project_frontend_language ADD CONSTRAINT FK_2CDBB36C166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_frontend_language ADD CONSTRAINT FK_2CDBB36CDF8632D0 FOREIGN KEY (frontend_language_id) REFERENCES frontend_language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_backend_language ADD CONSTRAINT FK_7BD1538B166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_backend_language ADD CONSTRAINT FK_7BD1538BD160DECF FOREIGN KEY (backend_language_id) REFERENCES backend_language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_tool ADD CONSTRAINT FK_1962F6C9166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_tool ADD CONSTRAINT FK_1962F6C98F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A166D1F9C');
        $this->addSql('ALTER TABLE project_frontend_language DROP FOREIGN KEY FK_2CDBB36C166D1F9C');
        $this->addSql('ALTER TABLE project_frontend_language DROP FOREIGN KEY FK_2CDBB36CDF8632D0');
        $this->addSql('ALTER TABLE project_backend_language DROP FOREIGN KEY FK_7BD1538B166D1F9C');
        $this->addSql('ALTER TABLE project_backend_language DROP FOREIGN KEY FK_7BD1538BD160DECF');
        $this->addSql('ALTER TABLE project_tool DROP FOREIGN KEY FK_1962F6C9166D1F9C');
        $this->addSql('ALTER TABLE project_tool DROP FOREIGN KEY FK_1962F6C98F7B22CC');
        $this->addSql('DROP TABLE backend_language');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE frontend_language');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE project_frontend_language');
        $this->addSql('DROP TABLE project_backend_language');
        $this->addSql('DROP TABLE project_tool');
        $this->addSql('DROP TABLE tool');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE rememberme_token');
    }
}
