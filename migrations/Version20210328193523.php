<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210328193523 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, idhotel_id INT DEFAULT NULL, idclient_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9474526C9896F315 (idhotel_id), INDEX IDX_9474526C67F0C0D4 (idclient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9896F315 FOREIGN KEY (idhotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C67F0C0D4 FOREIGN KEY (idclient_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF3243BB18');
        $this->addSql('DROP INDEX IDX_C509E4FF3243BB18 ON chambre');
        $this->addSql('ALTER TABLE chambre ADD numero INT NOT NULL, ADD image VARCHAR(255) NOT NULL, CHANGE hotel_id idhotel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF9896F315 FOREIGN KEY (idhotel_id) REFERENCES hotel (id)');
        $this->addSql('CREATE INDEX IDX_C509E4FF9896F315 ON chambre (idhotel_id)');
        $this->addSql('ALTER TABLE client ADD daten DATE NOT NULL, ADD num INT NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD adresse VARCHAR(255) NOT NULL, ADD mdp VARCHAR(255) NOT NULL, ADD cin INT NOT NULL');
        $this->addSql('ALTER TABLE hotel ADD adresse VARCHAR(255) NOT NULL, ADD nbetoile INT NOT NULL, ADD num INT NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD lat DOUBLE PRECISION DEFAULT NULL, ADD lng DOUBLE PRECISION DEFAULT NULL, CHANGE nom pays VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transport ADD image VARCHAR(255) NOT NULL, CHANGE prix_location prix INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF9896F315');
        $this->addSql('DROP INDEX IDX_C509E4FF9896F315 ON chambre');
        $this->addSql('ALTER TABLE chambre DROP numero, DROP image, CHANGE idhotel_id hotel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('CREATE INDEX IDX_C509E4FF3243BB18 ON chambre (hotel_id)');
        $this->addSql('ALTER TABLE client DROP daten, DROP num, DROP email, DROP adresse, DROP mdp, DROP cin');
        $this->addSql('ALTER TABLE hotel ADD nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP pays, DROP adresse, DROP nbetoile, DROP num, DROP email, DROP image, DROP lat, DROP lng');
        $this->addSql('ALTER TABLE transport DROP image, CHANGE prix prix_location INT NOT NULL');
    }
}
