<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210305212346 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre ADD idhotel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF9896F315 FOREIGN KEY (idhotel_id) REFERENCES hotel (id)');
        $this->addSql('CREATE INDEX IDX_C509E4FF9896F315 ON chambre (idhotel_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF9896F315');
        $this->addSql('DROP INDEX IDX_C509E4FF9896F315 ON chambre');
        $this->addSql('ALTER TABLE chambre DROP idhotel_id');
    }
}
