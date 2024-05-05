<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323052516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shares (ressource_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_905F717CFC6CD52A (ressource_id), INDEX IDX_905F717CA76ED395 (user_id), PRIMARY KEY(ressource_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shares ADD CONSTRAINT FK_905F717CFC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shares ADD CONSTRAINT FK_905F717CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource ADD active TINYINT(1) NOT NULL, ADD publication_date DATETIME DEFAULT NULL, CHANGE visibility visibility INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shares DROP FOREIGN KEY FK_905F717CFC6CD52A');
        $this->addSql('ALTER TABLE shares DROP FOREIGN KEY FK_905F717CA76ED395');
        $this->addSql('DROP TABLE shares');
        $this->addSql('ALTER TABLE ressource DROP active, DROP publication_date, CHANGE visibility visibility TINYINT(1) NOT NULL');
    }
}
