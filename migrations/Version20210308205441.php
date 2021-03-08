<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210308205441 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71669B19B');
        $this->addSql('DROP INDEX IDX_5FB6DEC71669B19B ON reponse');
        $this->addSql('ALTER TABLE reponse ADD rec INT DEFAULT NULL, DROP rec_id');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC76405CA2C FOREIGN KEY (rec) REFERENCES reclamation (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5FB6DEC76405CA2C ON reponse (rec)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC76405CA2C');
        $this->addSql('DROP INDEX IDX_5FB6DEC76405CA2C ON reponse');
        $this->addSql('ALTER TABLE reponse ADD rec_id INT NOT NULL, DROP rec');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71669B19B FOREIGN KEY (rec_id) REFERENCES reclamation (id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC71669B19B ON reponse (rec_id)');
    }
}
