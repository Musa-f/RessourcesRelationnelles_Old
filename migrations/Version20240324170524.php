<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324170524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file ADD ressource_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610FC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610FC6CD52A ON file (ressource_id)');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F454493CB796C');
        $this->addSql('DROP INDEX IDX_939F454493CB796C ON ressource');
        $this->addSql('ALTER TABLE ressource DROP file_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610FC6CD52A');
        $this->addSql('DROP INDEX IDX_8C9F3610FC6CD52A ON file');
        $this->addSql('ALTER TABLE file DROP ressource_id');
        $this->addSql('ALTER TABLE ressource ADD file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F454493CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('CREATE INDEX IDX_939F454493CB796C ON ressource (file_id)');
    }
}
