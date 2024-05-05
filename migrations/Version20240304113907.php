<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304113907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ressource_id INT DEFAULT NULL, content LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, visibility TINYINT(1) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526CFC6CD52A (ressource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE connection (id INT AUTO_INCREMENT NOT NULL, user1_id INT NOT NULL, user2_id INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_29F7736656AE248B (user1_id), INDEX IDX_29F77366441B8B65 (user2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, size INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE format (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT DEFAULT NULL, chat_id INT DEFAULT NULL, date_sent DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FCD53EDB6 (receiver_id), INDEX IDX_B6BD307F1A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, format_id INT NOT NULL, category_id INT NOT NULL, file_id INT DEFAULT NULL, user_id INT NOT NULL, title VARCHAR(100) NOT NULL, creation_date DATETIME NOT NULL, visibility TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_939F4544D629F605 (format_id), INDEX IDX_939F454412469DE2 (category_id), INDEX IDX_939F454493CB796C (file_id), INDEX IDX_939F4544A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE likes (ressource_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_49CA4E7DFC6CD52A (ressource_id), INDEX IDX_49CA4E7DA76ED395 (user_id), PRIMARY KEY(ressource_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saves (ressource_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7A3056E2FC6CD52A (ressource_id), INDEX IDX_7A3056E2A76ED395 (user_id), PRIMARY KEY(ressource_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE views (ressource_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_11F09C87FC6CD52A (ressource_id), INDEX IDX_11F09C87A76ED395 (user_id), PRIMARY KEY(ressource_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, login VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, creation_date DATETIME NOT NULL, deactivation_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F7736656AE248B FOREIGN KEY (user1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366441B8B65 FOREIGN KEY (user2_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F4544D629F605 FOREIGN KEY (format_id) REFERENCES format (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F454412469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F454493CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F4544A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DFC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saves ADD CONSTRAINT FK_7A3056E2FC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saves ADD CONSTRAINT FK_7A3056E2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87FC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C87A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFC6CD52A');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F7736656AE248B');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366441B8B65');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1A9A7125');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F4544D629F605');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F454412469DE2');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F454493CB796C');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F4544A76ED395');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DFC6CD52A');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DA76ED395');
        $this->addSql('ALTER TABLE saves DROP FOREIGN KEY FK_7A3056E2FC6CD52A');
        $this->addSql('ALTER TABLE saves DROP FOREIGN KEY FK_7A3056E2A76ED395');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87FC6CD52A');
        $this->addSql('ALTER TABLE views DROP FOREIGN KEY FK_11F09C87A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE connection');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE saves');
        $this->addSql('DROP TABLE views');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
